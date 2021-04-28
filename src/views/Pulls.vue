<template>
  <div>
    <p>Pulls: {{ userName }} / {{ projectName }} / pulls</p>
    <div ref="osmdContainer" />
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, onMounted } from "vue";
import format from "xml-formatter"
import { OpenSheetMusicDisplay } from "opensheetmusicdisplay";
// import score1 from "@/scores/score1";
import score1 from "@/scores/E12_intermission.musicxml";
import score2 from "@/scores/E12_intermission-1.musicxml";

export function longestCommonSubsequence(
  c: Element[],
  d: Element[]
): Element[] {
  // common final sequence
  // console.log(c)
  // console.log(d)
  for (const el of c) {
    while(el.attributes.length > 0)
    el.removeAttribute(el.attributes[0].name);
    console.log(format(el.outerHTML))
  }
  for (const el of d) {
    while(el.attributes.length > 0)
    el.removeAttribute(el.attributes[0].name);
    console.log(format(el.outerHTML))
  }
  // console.log(c[0], d[0])
  // console.log(c[0].isEqualNode(d[0]))

  c.push(document.createElement("div"));
  d.push(document.createElement("div"));

  const matrix = Array.from(
    new Array(c.length + 1),
    () => new Array(d.length + 1)
  );

  // console.log(matrix)

  function backtrack(
    c: Element[],
    d: Element[],
    x: number,
    y: number
  ): Element[] {
    if (x === 0 || y === 0) return [];
    return format(c[x - 1].outerHTML) === format(d[y - 1].outerHTML)
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
      matrix[i][j] =
        format(c[i - 1].outerHTML) === format(d[j - 1].outerHTML)
          ? matrix[i - 1][j - 1] + 1 // i-1, j-1
          : Math.max(matrix[i][j - 1], matrix[i - 1][j]);
    }
  }

  console.log(matrix)

  const result = backtrack(c, d, c.length, d.length);
  // remove final sequence
  c.pop();
  d.pop();
  result.pop();
  return result;
}

export default defineComponent({
  props: {
    userName: String,
    projectName: String,
  },
  setup() {
    const osmdContainer = ref<HTMLElement>();
    const parser = new DOMParser();

    const actual = parser.parseFromString(score2, "text/xml");
    const base = parser.parseFromString(score1, "text/xml");
    const diff = parser.parseFromString(score1, "text/xml");

    const parts_a = actual.getElementsByTagName("part");
    const parts_b = base.getElementsByTagName("part");
    const parts_d = diff.getElementsByTagName("part");
    for (let i_p = 0; i_p < /* parts_d.length */1; i_p++) {
      const measures_a = parts_a[i_p].getElementsByTagName("measure");
      const measures_b = parts_b[i_p].getElementsByTagName("measure");
      const measures_d = parts_d[i_p].getElementsByTagName("measure");
      for (let i_m = 0; i_m < /* measures_d.length */1; i_m++) {
        const notes_a = measures_a[i_m].children;
        const notes_b = measures_b[i_m].children;
        const notes = measures_d[i_m];

        while (notes.firstChild) {
          notes.firstChild.remove();
        }
        const LCS = longestCommonSubsequence([...notes_a], [...notes_b]);
        let actualIndex = 0;
        let baseIndex = 0;

        for (let i = 0; i <= LCS.length; i++) {
          while (
            LCS[i] !== undefined &&
            format(LCS[i].outerHTML) !== format(notes_b[baseIndex].outerHTML) &&
            baseIndex < notes_b.length
          ) {
            if (
              format(LCS[i].outerHTML) !== format(notes_a[actualIndex].outerHTML) &&
              actualIndex < notes_a.length
            ) {
              // diff.push(compare_(actual[actualIndex], base[baseIndex]));
              // modified
              const note = notes_a[actualIndex].cloneNode(true) as Element;
              note.setAttribute("color", "#FFAA00");
              notes.appendChild(note);
              actualIndex++;
            } else {
              const note = notes_b[baseIndex].cloneNode(true) as Element;
              note.setAttribute("color", "#FF0000");
            notes.appendChild(note);
            }
            baseIndex++;
          }
          while (
            LCS[i] !== undefined &&
            format(LCS[i].outerHTML) !== format(notes_a[actualIndex].outerHTML) &&
            actualIndex < notes_a.length
          ) {
            const note = notes_a[actualIndex].cloneNode(true) as Element;
            note.setAttribute("color", "#00FF00");
            notes.appendChild(note);
            actualIndex++;
          }
          if (LCS[i] !== undefined) {
            notes.appendChild(LCS[i].cloneNode(true)); //fiks //Nani
          }
          baseIndex++;
          actualIndex++;
        }
      }
    }
    // console.log(actual.getElementsByTagName("part")[0].getElementsByTagName("measure")[0])
    // console.log(base.getElementsByTagName("part")[0].getElementsByTagName("measure")[0])
    // const notes$ = actual.getElementsByTagName("part")[0].getElementsByTagName("measure")[0].children
    // const notes_ = base.getElementsByTagName("part")[0].getElementsByTagName("measure")[0].children
    // console.log(notes$)
    // console.log(diff);
    // console.log(measure);
    // const notes = measure.getElementsByTagName("note");
    // const note = notes_[0]
    // const note0 = notes_[0].cloneNode(true);
    // notes[0].setAttribute("color", "#FF0000");
    // console.log(notes[0].isEqualNode(notes[1]));
    // console.log(notes_[3])
    // console.log(notes$[3])
    // console.log(format(notes_[3].outerHTML) === format(notes_[4].outerHTML));
    // console.log(format(notes$[3].outerHTML)=== format(notes$[4].outerHTML));
    // console.log(format(notes$[3].outerHTML)=== format(notes_[3].outerHTML));
    // console.log(notes_[3].isSameNode(notes_[4]));
    // console.log(notes$[3].isSameNode(notes$[4]));
    // console.log(notes$[3].isSameNode(notes_[3]));
    // console.log(note0, notes[0], notes[1]);

    async function loadScore() {
      const osmd = new OpenSheetMusicDisplay(osmdContainer.value!, {
        backend: "svg",
        drawTitle: true,
        drawCredits: true,
      });

      await osmd.load(diff);
      osmd.render();
    }
    onMounted(loadScore);

    return { osmdContainer };
  },
});
</script>
