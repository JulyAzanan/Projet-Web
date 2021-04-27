import { Cursor } from "opensheetmusicdisplay";

Cursor.prototype.update = function () {
  if (this.hidden || this.hidden === undefined || this.hidden === null) {
    return;
  }
  this.updateCurrentPage(); // attach cursor to new page DOM if necessary

  // this.graphic?.Cursors?.length = 0;
  const iterator = this.iterator;
  // TODO when measure draw range (drawUpToMeasureNumber) was changed, next/update can fail to move cursor. but of course it can be reset before.

  const voiceEntries = iterator.CurrentVisibleVoiceEntries();
  if (iterator.EndReached || !iterator.CurrentVoiceEntries || voiceEntries.length === 0) {
    return;
  }
  let x = 0, y = 0, height = 0;
  let musicSystem;
  if (iterator.CurrentMeasure.isReducedToMultiRest) {
    const multiRestGMeasure = this.graphic.findGraphicalMeasure(iterator.CurrentMeasureIndex, 0);
    const totalRestMeasures = multiRestGMeasure.parentSourceMeasure.multipleRestMeasures;
    const currentRestMeasureNumber = iterator.CurrentMeasure.multipleRestMeasureNumber;
    const progressRatio = currentRestMeasureNumber / (totalRestMeasures + 1);
    const effectiveWidth = multiRestGMeasure.PositionAndShape.Size.width - (multiRestGMeasure).beginInstructionsWidth;
    x = multiRestGMeasure.PositionAndShape.AbsolutePosition.x + (multiRestGMeasure).beginInstructionsWidth + progressRatio * effectiveWidth;

    musicSystem = multiRestGMeasure.ParentMusicSystem;
  } else {
        // get all staff entries inside the current voice entry
        const gseArr = voiceEntries.map(ve => this.getStaffEntryFromVoiceEntry(ve));
        // sort them by x position and take the leftmost entry
        const gse =
              gseArr.sort((a, b) => a?.PositionAndShape?.AbsolutePosition?.x <= b?.PositionAndShape?.AbsolutePosition?.x ? -1 : 1 )[0];
        x = gse.PositionAndShape.AbsolutePosition.x;
        musicSystem = gse.parentMeasure.ParentMusicSystem;

        // debug: change color of notes under cursor (needs re-render)
        // for (const gve of gse.graphicalVoiceEntries) {
        //   for (const note of gve.notes) {
        //     note.sourceNote.NoteheadColor = "#0000FF";
        //   }
        // }
  }
  if (!musicSystem) {
    return;
  }

  // y is common for both multirest and non-multirest, given the MusicSystem
  y = musicSystem.PositionAndShape.AbsolutePosition.y + musicSystem.StaffLines[0].PositionAndShape.RelativePosition.y;
  const bottomStaffline = musicSystem.StaffLines[musicSystem.StaffLines.length - 1];
  const endY = musicSystem.PositionAndShape.AbsolutePosition.y +
  bottomStaffline.PositionAndShape.RelativePosition.y + bottomStaffline.StaffHeight;
  height = endY - y;

  // Update the graphical cursor
  // The following is the legacy cursor rendered on the canvas:
  // // let cursor: GraphicalLine = new GraphicalLine(new PointF2D(x, y), new PointF2D(x, y + height), 3, OutlineAndFillStyleEnum.PlaybackCursor);

  // This the current HTML Cursor:
  const cursorElement = this.cursorElement;
  cursorElement.style.top = (y * 10.0 * this.openSheetMusicDisplay.zoom) + "px";
  cursorElement.style.left = ((x - 1.5) * 10.0 * this.openSheetMusicDisplay.zoom) + "px";
  cursorElement.height = (height * 10.0 * this.openSheetMusicDisplay.zoom);
  cursorElement.style.height = cursorElement.height + "px";
  const newWidth = 3 * 10.0 * this.openSheetMusicDisplay.zoom;
  if (newWidth !== cursorElement.width) {
    cursorElement.width = newWidth;
    this.updateStyle(newWidth);
  }
  if (this.openSheetMusicDisplay.FollowCursor) {
    const diff = this.cursorElement.getBoundingClientRect().top;
    this.cursorElement.scrollIntoView({behavior: diff < 1000 ? "smooth" : "auto", block: "center"});
  }
  // Show cursor
  // // Old cursor: this.graphic.Cursors.push(cursor);
  this.cursorElement.style.display = "";
};