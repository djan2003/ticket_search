import { renderOgImage, OG_SIZE, OG_CONTENT_TYPE } from "../lib/og";
import { SITE } from "../lib/site";

export const size = OG_SIZE;
export const contentType = OG_CONTENT_TYPE;
export const alt = SITE.title;

export default function Image() {
  return renderOgImage(
    "Дешёвые авиабилеты на выходные",
    "из Батуми, Тбилиси и Еревана",
  );
}
