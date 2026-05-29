export type Flight = {
  origin: string;
  destination: string;
  departure_date: string;
  return_date: string | null;
  price: number;
  currency: string;
  link: string;
  airline: string | null;
  stops: number | null;
};

export type OriginFeed = {
  generated_at: string;
  results: Flight[];
};

export type Feed = Record<string, OriginFeed>;
