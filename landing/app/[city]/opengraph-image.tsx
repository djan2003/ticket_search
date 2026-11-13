import { renderOgImage, OG_SIZE, OG_CONTENT_TYPE } from "../../lib/og";
import {
  DESTINATIONS,
  destinationBySlug,
  joinRu,
  originsForDestination,
} from "../../lib/destinations";

export const size = OG_SIZE;
export const contentType = OG_CONTENT_TYPE;
export const alt = "Дешёвые авиабилеты на выходные";
export const dynamicParams = false;

export function generateStaticParams() {
  return DESTINATIONS.map((d) => ({ city: d.slug }));
}

export default async function Image({
  params,
}: {
  params: Promise<{ city: string }>;
}) {
  const { city } = await params;
  const dest = destinationBySlug(city);
  const origins = dest ? originsForDestination(dest) : [];
  return renderOgImage(
    `Авиабилеты в ${dest?.nameRu ?? "город"}`,
    origins.length <= 4
      ? `на выходные · из ${joinRu(origins.map((o) => o.nameGen))}`
      : "на выходные · без визы",
  );
}
