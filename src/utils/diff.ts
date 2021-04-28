import format from "xml-formatter";

interface Note {
  pitch: string,
  duration: string,
  voice: string,
  type: string,
}

const colors = {
  added: "#00FF00",
  removed: "#FF0000",
  modified: "#FFAA00"
}

const finalSequence: Note = {
  pitch: "",
  duration: "",
  voice: "",
  type: "",
};

function removeAttributes(element: Element): Element {
  const el = element.cloneNode(true) as Element;
  while (el.attributes.length > 0)
    el.removeAttribute(el.attributes[0].name);
  return el;
}

function equals(a?: Note, b?: Note) {
  return a && b && (
    a.duration === b.duration &&
    a.pitch === b.pitch &&
    a.type === b.type &&
    a.voice === b.voice
  );
}

function toNote(el: Element): Note {
  const pitch = el.getElementsByTagName("pitch");
  const duration = el.getElementsByTagName("duration");
  const voice = el.getElementsByTagName("voice");
  const type = el.getElementsByTagName("type");
  return {
    pitch: pitch.length > 0 ? format(pitch[0].outerHTML) : "",
    duration: duration.length > 0 ? format(duration[0].outerHTML) : "",
    voice: voice.length > 0 ? format(voice[0].outerHTML) : "",
    type: type.length > 0 ? format(type[0].outerHTML) : "",
  }
}

function longestCommonSubsequence(c: Note[], d: Note[]): Note[] {
  // common final sequence
  c.push(finalSequence);
  d.push(finalSequence);

  const matrix = Array.from(
    new Array(c.length + 1),
    () => new Array(d.length + 1)
  );

  function backtrack(c: Note[], d: Note[], x: number, y: number): Note[] {
    if (x === 0 || y === 0) return [];
    return equals(c[x - 1], d[y - 1])
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
      matrix[i][j] = equals(c[i - 1], d[j - 1])
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
  if (measure.children.length === 0) return [];
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

function markNotesAdded(notes: Element[], diff: Element): void {
  for (const element of notes) {
    if (element.tagName === "note") {
      const el = element.cloneNode(true) as Element;
      el.setAttribute("color", colors.added);
      diff.appendChild(el);
    }
  }
}

function markNotesRemoved(notes: Element[], diff: Element): void {
  for (const element of notes) {
    if (element.tagName === "note") {
      const el = element.cloneNode(true) as Element;
      el.setAttribute("color", colors.removed);
      diff.appendChild(el);
    }
  }
}

export function measureDiff(measure_a: Element, measure_b: Element, diff: Element): void {
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

function noteDiff(notes_a: Element[], notes_b: Element[], notes: Element): void {
  const xml_a: Note[] = [];
  const xml_b: Note[] = [];
  for (const el of notes_a) {
    xml_a.push(toNote(removeAttributes(el)));
  }
  for (const el of notes_b) {
    xml_b.push(toNote(removeAttributes(el)));
  }

  const LCS = longestCommonSubsequence(xml_a, xml_b);
  let actualIndex = 0;
  let baseIndex = 0;

  for (let i = 0; i <= LCS.length; i++) {
    while (
      !equals(LCS[i], xml_b[baseIndex]) &&
      baseIndex < notes_b.length
    ) {
      if (
        !equals(LCS[i], xml_a[actualIndex]) &&
        actualIndex < notes_a.length
      ) {
        const note = notes_a[actualIndex].cloneNode(true) as Element;
        note.setAttribute("color", colors.modified);
        notes.appendChild(note);
        actualIndex++;
      } else {
        const note = notes_b[baseIndex].cloneNode(true) as Element;
        note.setAttribute("color", colors.removed);
        notes.appendChild(note);
      }
      baseIndex++;
    }
    while (
      !equals(LCS[i], xml_a[actualIndex]) &&
      actualIndex < notes_a.length
    ) {
      const note = notes_a[actualIndex].cloneNode(true) as Element;
      note.setAttribute("color", colors.added);
      notes.appendChild(note);
      actualIndex++;
    }
    if (LCS[i] !== undefined) {
      notes.appendChild(notes_a[actualIndex].cloneNode(true));
    }
    baseIndex++;
    actualIndex++;
  }
}
