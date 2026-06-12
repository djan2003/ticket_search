import { renderOgImage, OG_SIZE, OG_CONTENT_TYPE } from "../../lib/og";
import { DESTINATIONS, destinationBySlug } from "../../lib/destinations";

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
  return renderOgImage(
    `Авиабилеты в ${dest?.nameRu ?? "город"}`,
    "на выходные · из Батуми, Тбилиси и Еревана",
  );
}
