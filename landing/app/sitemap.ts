import type { MetadataRoute } from "next";
import { DESTINATIONS, ORIGIN_LIST } from "../lib/destinations";
import { SITE } from "../lib/site";

export default function sitemap(): MetadataRoute.Sitemap {
  const now = new Date();
  return [
    { url: SITE.url, lastModified: now, priority: 1 },
    ...ORIGIN_LIST.map((o) => ({
      url: `${SITE.url}/from/${o.slug}`,
      lastModified: now,
      priority: 0.9,
    })),
    ...DESTINATIONS.map((d) => ({
      url: `${SITE.url}/${d.slug}`,
      lastModified: now,
      priority: 0.8,
    })),
  ];
}
