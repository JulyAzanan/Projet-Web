import format from "xml-formatter";

interface Note {
  misc: Element[],
  pitch: string,
  voice: string,
  type: string,
}

const colors = {
  added: "#69B32B",
  removed: "#F94144",
  modified: "#F9C74F"
}

function removeAttributes_(el: Element): void {
  while (el.attributes.length > 0)
    el.removeAttribute(el.attributes[0].name);
}

function removeAttributes(element: Element): Element {
  const el = element.cloneNode(true) as Element;
  removeAttributes_(el);
  return el;
}

function noteEquals(a?: Note, b?: Note) {
  return a && b && (
    a.pitch === b.pitch &&
    a.type === b.type &&
    a.voice === b.voice
  );
}

function toNote(el: Element): Note {
  const pitch_ = el.getElementsByTagName("pitch");
  const voice_ = el.getElementsByTagName("voice");
  const type_ = el.getElementsByTagName("type");
  const pitch = pitch_.length > 0 ? format(pitch_[0].outerHTML) : "";
  const voice = voice_.length > 0 ? format(voice_[0].outerHTML) : "";
  const type = type_.length > 0 ? format(type_[0].outerHTML) : "";
  const tags = ["pitch", "duration", "voice", "type"]
  const misc = [...el.children].filter((e) => !tags.includes(e.tagName));
  return {
    pitch, voice, type, misc,
  }
}

const finalNoteSequence: Note = {
  misc: [],
  pitch: "",
  voice: "",
  type: "",
};

function longestCommonNoteSubsequence(c: Note[], d: Note[]): Note[] {
  // common final sequence
  c.push(finalNoteSequence);
  d.push(finalNoteSequence);

  const matrix = Array.from(
    new Array(c.length + 1),
    () => new Array(d.length + 1)
  );

  function backtrack(c: Note[], d: Note[], x: number, y: number): Note[] {
    if (x === 0 || y === 0) return [];
    return noteEquals(c[x - 1], d[y - 1])
      ? backtrack(c, d, x - 1, y - 1).concat(c[x - 1]) // x-1, y-1
      : matrix[x][y - 1] > matrix[x - 1][y]
        ? backtrack(c, d, x, y - 1)
        : backtrack(c, d, x - 1, y);
  }

  for (let i = 0; i < c.length; i++) {
    matrix[i][0] = 0;
  }
  for (let j = 0; j < d.length; j++) {
    matrix[0][j] = 0;
  }
  for (let i = 1; i <= c.length; i++) {
    for (let j = 1; j <= d.length; j++) {
      matrix[i][j] = noteEquals(c[i - 1], d[j - 1])
        ? matrix[i - 1][j - 1] + 1 // i-1, j-1
        : Math.max(matrix[i][j - 1], matrix[i - 1][j]);
    }
  }

  const result = backtrack(c, d, c.length, d.length);
  // remove final sequence
  c.pop();
  d.pop();
  result.pop();
  return result;
}

function measureToString(element: Element): string {
  const el = element.cloneNode(true) as Element;
  removeAttributes_(el);
  for (const e of el.children) {
    if (e.tagName === "note") removeAttributes_(e)
  }
  return format(el.outerHTML);
}

function longestCommonSubsequence(c: string[], d: string[]): string[] {
  // common final sequence
  c.push("");
  d.push("");

  const matrix = Array.from(
    new Array(c.length + 1),
    () => new Array(d.length + 1)
  );

  function backtrack(c: string[], d: string[], x: number, y: number): string[] {
    if (x === 0 || y === 0) return [];
    return c[x - 1] === d[y - 1]
      ? backtrack(c, d, x - 1, y - 1).concat(c[x - 1]) // x-1, y-1
      : matrix[x][y - 1] > matrix[x - 1][y]
        ? backtrack(c, d, x, y - 1)
        : backtrack(c, d, x - 1, y);
  }

  for (let i = 0; i < c.length; i++) {
    matrix[i][0] = 0;
  }
  for (let j = 0; j < d.length; j++) {
    matrix[0][j] = 0;
  }
  for (let i = 1; i <= c.length; i++) {
    for (let j = 1; j <= d.length; j++) {
      matrix[i][j] = c[i - 1] === d[j - 1]
        ? matrix[i - 1][j - 1] + 1 // i-1, j-1
        : Math.max(matrix[i][j - 1], matrix[i - 1][j]);
    }
  }

  const result = backtrack(c, d, c.length, d.length);
  // remove final sequence
  c.pop();
  d.pop();
  result.pop();
  return result;
}

type NoteArray = {
  elements: Element[],
  type: "properties"
} | {
  elements: Element[],
  type: "symbols"
}

function splitMeasure(measure: Element): NoteArray[] {
  if (measure === undefined || measure.children.length === 0) return [];
  const notes: NoteArray[] = [];
  let mode = "init";
  for (const element of measure.children) {
    if (element.tagName === "note") {
      if (mode !== "symbols") notes.push({
        elements: [],
        type: "symbols",
      });
      mode = "symbols";
    } else {
      if (mode !== "properties") notes.push({
        elements: [],
        type: "properties",
      });
      mode = "properties";
    }
    notes[notes.length - 1].elements.push(element);
  }
  return notes;
}

function markMeasuresAdded(notes: Element[] | HTMLCollection, diff: Element): void {
  for (const element of notes) {
    const el = element.cloneNode(true) as Element;
    diff.appendChild(removeChildren(el.cloneNode(true) as Element));
    markNotesAdded(el.children, diff.lastChild as Element);
  }
}

function markNotesAdded(notes: Element[] | HTMLCollection, diff: Element): void {
  for (const element of notes) {
    const el = element.cloneNode(true) as Element;
    if (element.tagName === "note") {
      el.setAttribute("color", colors.added);
    }
    diff.appendChild(el);
  }
}

function markMeasuresRemoved(notes: Element[] | HTMLCollection, diff: Element): void {
  for (const element of notes) {
    const el = element.cloneNode(true) as Element;
    diff.appendChild(removeChildren(el.cloneNode(true) as Element));
    markNotesRemoved(el.children, diff.lastChild as Element);
  }
}

function markNotesRemoved(notes: Element[] | HTMLCollection, diff: Element): void {
  for (const element of notes) {
    const el = element.cloneNode(true) as Element;
    if (element.tagName === "note") {
      el.setAttribute("color", colors.removed);
    }
    diff.appendChild(el);
  }
}

function removeChildren(el: Element): Element {
  while (el.firstChild) {
    el.firstChild.remove();
  }
  return el;
}

function setPartNumber(el: Element, n: number) {
  if (el.hasAttribute("id")) {
    el.setAttribute("id", el.getAttribute("id")!.replace(/P\d+/, `P${n}`))
  }
  for (const child of el.children) {
    setPartNumber(child, n);
  }
}

export function scoreDiff(score_a: Element, score_b: Element, diff: Element): void {
  const diffPartList = diff.getElementsByTagName("part-list")[0];

  while (diff.getElementsByTagName("part").length > 0) {
    diff.getElementsByTagName("part")[0].remove();
  }
  while (diffPartList.children.length > 0) {
    diffPartList.children[0].remove();
  }

  const scoreParts_a = score_a.getElementsByTagName("part-list")[0].getElementsByTagName("score-part");
  const scoreParts_b = score_b.getElementsByTagName("part-list")[0].getElementsByTagName("score-part");
  const parts_a = score_a.getElementsByTagName("part");
  const parts_b = score_b.getElementsByTagName("part");
  const partNames_a: string[] = [];
  const partNames_b: string[] = [];

  for (const el of scoreParts_a) {
    partNames_a.push(el.getElementsByTagName("part-name")[0].firstChild!.textContent!);
  }
  for (const el of scoreParts_b) {
    partNames_b.push(el.getElementsByTagName("part-name")[0].firstChild!.textContent!);
  }

  const LCS = longestCommonSubsequence(partNames_a, partNames_b);
  let actualIndex = 0;
  let baseIndex = 0;

  let partNumber = 1;
  for (let i = 0; i <= LCS.length; i++) {
    while (
      LCS[i] !== partNames_b[baseIndex] &&
      baseIndex < partNames_b.length
    ) {
      if (
        LCS[i] !== partNames_a[actualIndex] &&
        actualIndex < partNames_a.length
      ) {
        const scorePart = diffPartList.appendChild(scoreParts_a[actualIndex].cloneNode(true)) as Element;
        setPartNumber(scorePart, partNumber);
        const part = diff.appendChild(parts_a[actualIndex].cloneNode(true)) as Element;
        partDiff(parts_a[actualIndex].children, parts_b[baseIndex].children, diff.lastChild as Element);
        part.setAttribute("id", `P${partNumber}`);
        actualIndex++;
      } else {
        const scorePart = diffPartList.appendChild(scoreParts_b[baseIndex].cloneNode(true)) as Element;
        setPartNumber(scorePart, partNumber);
        const part = diff.appendChild(removeChildren(parts_b[baseIndex].cloneNode(true) as Element));
        markMeasuresRemoved(parts_b[baseIndex].children, diff.lastChild as Element);
        part.setAttribute("id", `P${partNumber}`);
      }
      baseIndex++;
      partNumber++;
    }
    while (
      LCS[i] !== partNames_a[actualIndex] &&
      actualIndex < partNames_a.length
    ) {
      const scorePart = diffPartList.appendChild(scoreParts_a[actualIndex].cloneNode(true)) as Element;
      setPartNumber(scorePart, partNumber);
      const part = diff.appendChild(parts_a[actualIndex].cloneNode(true)) as Element;
      markMeasuresAdded(parts_a[actualIndex].children, diff.lastChild as Element);
      part.setAttribute("id", `P${partNumber}`);
      actualIndex++;
      partNumber++;
    }
    if (LCS[i] !== undefined) {
      const scorePart = diffPartList.appendChild(scoreParts_a[actualIndex].cloneNode(true)) as Element;
      setPartNumber(scorePart, partNumber);
      const part = diff.appendChild(parts_a[actualIndex].cloneNode(true)) as Element;
      partDiff(parts_a[actualIndex].children, parts_b[baseIndex].children, diff.lastChild as Element);
      part.setAttribute("id", `P${partNumber}`);
    }
    baseIndex++;
    actualIndex++;
    partNumber++;
  }
}

export function partDiff(part_a: HTMLCollectionOf<Element>, part_b: HTMLCollectionOf<Element>, diff: Element): void {
  removeChildren(diff);

  const measures_a: string[] = [];
  const measures_b: string[] = [];
  for (const el of part_a) {
    measures_a.push(measureToString(el));
  }
  for (const el of part_b) {
    measures_b.push(measureToString(el));
  }

  const LCS = longestCommonSubsequence(measures_a, measures_b);
  let actualIndex = 0;
  let baseIndex = 0;

  let measureNumber = 1;
  for (let i = 0; i <= LCS.length; i++) {
    while (
      LCS[i] !== measures_b[baseIndex] &&
      baseIndex < measures_b.length
    ) {
      if (
        LCS[i] !== measures_a[actualIndex] &&
        actualIndex < measures_a.length
      ) {
        const measure = diff.appendChild(removeChildren(part_a[actualIndex].cloneNode(true) as Element));
        measureDiff(part_a[actualIndex], part_b[baseIndex], diff.lastChild as Element);
        measure.setAttribute("number", `${measureNumber}`)
        actualIndex++;
      } else {
        const measure = diff.appendChild(removeChildren(part_b[baseIndex].cloneNode(true) as Element));
        markNotesRemoved(part_b[baseIndex].children, diff.lastChild as Element);
        measure.setAttribute("number", `${measureNumber}`)
      }
      baseIndex++;
      measureNumber++;
    }
    while (
      LCS[i] !== measures_a[actualIndex] &&
      actualIndex < measures_a.length
    ) {
      const measure = diff.appendChild(removeChildren(part_a[actualIndex].cloneNode(true) as Element));
      markNotesAdded(part_a[actualIndex].children, diff.lastChild as Element);
      measure.setAttribute("number", `${measureNumber}`)
      actualIndex++;
      measureNumber++;
    }
    if (LCS[i] !== undefined) {
      diff.appendChild(part_b[baseIndex].cloneNode(true));
      const measure = diff.lastChild as Element;
      measure.setAttribute("number", `${measureNumber}`)
    }
    baseIndex++;
    actualIndex++;
    measureNumber++;
  }
}

function measureDiff(measure_a: Element, measure_b: Element, diff: Element): void {
  while (diff.firstChild) {
    diff.firstChild.remove();
  }

  const split_a = splitMeasure(measure_a);
  const split_b = splitMeasure(measure_b);

  let j = 0;
  for (let i = 0; i < split_a.length; i++) {
    while (split_b[j] !== undefined && split_b[j].type !== "symbols") j++;
    if (split_a[i].type === "properties") {
      diff.append(...split_a[i].elements);
    } else {
      if (split_b[j] !== undefined) {
        noteDiff(split_a[i].elements, split_b[j].elements, diff);
      } else {
        markNotesAdded(split_a[i].elements, diff);
      }
      j++;
    }
  }
  for (let i = j; i < split_b.length; i++) {
    if (split_b[i].type === "properties") {
      diff.append(...split_b[i].elements);
    } else {
      markNotesRemoved(split_b[i].elements, diff);
    }
  }
}

function appendNote(original: Element, diff: Element): Element {
  const note = original.cloneNode(true) as Element;
  diff.appendChild(note);
  return note;
}

function checkRest(diff: Element) {
  const lastChild = diff.lastElementChild;
  const preChild = diff.children.item(diff.children.length - 2)
  if (lastChild && preChild) {
    if (preChild.getElementsByTagName("rest").length > 0) {
      const chords = lastChild.getElementsByTagName("chord");
      for (const chord of chords) {
        chord.remove();
      }
    }
  }
}

function noteDiff(notes_a: Element[], notes_b: Element[], diff: Element): void {
  const xml_a: Note[] = [];
  const xml_b: Note[] = [];
  for (const el of notes_a) {
    xml_a.push(toNote(removeAttributes(el)));
  }
  for (const el of notes_b) {
    xml_b.push(toNote(removeAttributes(el)));
  }

  const LCS = longestCommonNoteSubsequence(xml_a, xml_b);
  let actualIndex = 0;
  let baseIndex = 0;

  for (let i = 0; i <= LCS.length; i++) {
    while (
      !noteEquals(LCS[i], xml_b[baseIndex]) &&
      baseIndex < notes_b.length
    ) {
      if (
        !noteEquals(LCS[i], xml_a[actualIndex]) &&
        actualIndex < notes_a.length
      ) {
        appendNote(notes_a[actualIndex], diff).setAttribute("color", colors.modified);
        actualIndex++;
      } else {
        appendNote(notes_b[baseIndex], diff).setAttribute("color", colors.removed);
      }
      baseIndex++;
      checkRest(diff);
    }
    while (
      !noteEquals(LCS[i], xml_a[actualIndex]) &&
      actualIndex < notes_a.length
    ) {
      appendNote(notes_a[actualIndex], diff).setAttribute("color", colors.added);
      actualIndex++;
      checkRest(diff);
    }
    if (LCS[i] !== undefined) {
      appendNote(notes_b[baseIndex], diff).append(...LCS[i].misc);
      checkRest(diff);
    }
    baseIndex++;
    actualIndex++;
  }
}
