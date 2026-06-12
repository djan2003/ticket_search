import { renderOgImage, OG_SIZE, OG_CONTENT_TYPE } from "../../../lib/og";
import { ORIGIN_LIST, originBySlug } from "../../../lib/destinations";

export const size = OG_SIZE;
export const contentType = OG_CONTENT_TYPE;
export const alt = "Дешёвые авиабилеты на выходные";
export const dynamicParams = false;

export function generateStaticParams() {
  return ORIGIN_LIST.map((o) => ({ origin: o.slug }));
}

export default async function Image({
  params,
}: {
  params: Promise<{ origin: string }>;
}) {
  const { origin } = await params;
  const city = originBySlug(origin);
  return renderOgImage(
    `Авиабилеты из ${city?.nameRu ?? "города"}`,
    "на выходные · по безвизовым направлениям",
  );
}
