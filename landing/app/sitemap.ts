import type { MetadataRoute } from "next";
import { DESTINATIONS, ORIGIN_LIST } from "../lib/destinations";
import { getFeed, allFlights, flightsFromOrigin } from "../lib/feed";
import { SITE } from "../lib/site";

// Reads the live feed, so pages without flights are left out (they are noindex).
export const dynamic = "force-dynamic";

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const now = new Date();
  const feed = await getFeed();
  const withFlights = new Set(allFlights(feed).map((f) => f.destination));
  // An entirely empty feed means the fetch failed: list everything rather
  // than dropping the whole site from the sitemap.
  const feedOk = withFlights.size > 0;

  // Prices refresh daily, so pages are marked as daily-changing.
  return [
    { url: SITE.url, lastModified: now, changeFrequency: "daily", priority: 1 },
    ...ORIGIN_LIST.filter(
      (o) => !feedOk || flightsFromOrigin(feed, o.iata).length > 0,
    ).map((o) => ({
      url: `${SITE.url}/from/${o.slug}`,
      lastModified: now,
      changeFrequency: "daily" as const,
      priority: 0.9,
    })),
    ...DESTINATIONS.filter((d) => !feedOk || withFlights.has(d.iata)).map(
      (d) => ({
        url: `${SITE.url}/${d.slug}`,
        lastModified: now,
        changeFrequency: "daily" as const,
        priority: 0.8,
      }),
    ),
  ];
}
