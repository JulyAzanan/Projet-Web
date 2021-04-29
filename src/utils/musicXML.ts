import { InputFileFormat } from "webmscore/schemas";
import WebMscore from "webmscore";

const parser = new DOMParser();

function base64ToUint8Array(data: string) {
  const binary = atob(data);
  const array = new Uint8Array(new ArrayBuffer(binary.length));

  for (let i = 0; i < binary.length; i++) {
    array[i] = binary.charCodeAt(i);
  }
  return array;
}

async function toXml(extension: InputFileFormat, data: string) {
  await WebMscore.ready;
  const webMscore = await WebMscore.load(extension, base64ToUint8Array(data));
  return webMscore.saveXml();
}

export async function parseScore(extension: string | undefined, data: string): Promise<Document | null> {
  let xml = "";

  switch (extension) {
    case "musicxml":
    case "xml":
      xml = atob(data);
      break;

    case "ptb":
    case "gp":
    case "gpx":
    case "gp5":
    case "gp4":
    case "gp3":
    case "gtp":
    case "kar":
    case "midi":
    case "mscx":
    case "mscz":
      xml = await toXml(extension, data);
      break;
    default:
      return null;
  }

  return parser.parseFromString(xml, "text/xml");
}
