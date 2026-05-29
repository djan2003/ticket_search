import type { Feed, Flight } from "./types";

/**
 * Fetch the feed from the backend. Read fresh on each request (no-store):
 * the feed is a small JSON served over the internal network, so this avoids
 * stale/empty pages after deploy. Never throws: on any failure returns an
 * empty feed so pages still render.
 */
export async function getFeed(): Promise<Feed> {
  const url = process.env.API_FEED_URL;
  if (!url) return {};

  try {
    const res = await fetch(url, { cache: "no-store" });
    if (!res.ok) return {};
    const json = await res.json();
    return (json?.feed ?? {}) as Feed;
  } catch {
    return {};
  }
}

export function allFlights(feed: Feed): Flight[] {
  return Object.values(feed).flatMap((o) => o?.results ?? []);
}

export function flightsFromOrigin(feed: Feed, origin: string): Flight[] {
  return feed[origin]?.results ?? [];
}

/** Cheapest flight per destination, sorted by price ascending. */
export function cheapestPerDestination(flights: Flight[]): Flight[] {
  const byDest = new Map<string, Flight>();
  for (const f of flights) {
    const current = byDest.get(f.destination);
    if (!current || f.price < current.price) {
      byDest.set(f.destination, f);
    }
  }
  return [...byDest.values()].sort((a, b) => a.price - b.price);
}

/** All flights to a given destination IATA, sorted by price ascending. */
export function flightsToDestination(flights: Flight[], iata: string): Flight[] {
  return flights
    .filter((f) => f.destination === iata)
    .sort((a, b) => a.price - b.price);
}

export function latestGeneratedAt(feed: Feed): string | null {
  const dates = Object.values(feed)
    .map((o) => o?.generated_at)
    .filter(Boolean) as string[];
  if (dates.length === 0) return null;
  return dates.sort().at(-1) ?? null;
}
