import type { MetadataRoute } from "next";
import { DESTINATIONS, ORIGIN_LIST } from "../lib/destinations";
import { SITE } from "../lib/site";

export default function sitemap(): MetadataRoute.Sitemap {
  const now = new Date();
  // Prices refresh daily, so pages are marked as daily-changing.
  return [
    { url: SITE.url, lastModified: now, changeFrequency: "daily", priority: 1 },
    ...ORIGIN_LIST.map((o) => ({
      url: `${SITE.url}/from/${o.slug}`,
      lastModified: now,
      changeFrequency: "daily" as const,
      priority: 0.9,
    })),
    ...DESTINATIONS.map((d) => ({
      url: `${SITE.url}/${d.slug}`,
      lastModified: now,
      changeFrequency: "daily" as const,
      priority: 0.8,
    })),
  ];
}
