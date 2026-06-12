// IATA carrier code -> display name. Covers the airlines that actually fly
// the visa-free routes from Batumi / Tbilisi / Yerevan. Unknown codes fall
// back to the raw code so the column is never empty-looking.
const AIRLINES: Record<string, string> = {
  TK: "Turkish Airlines",
  PC: "Pegasus",
  W6: "Wizz Air",
  W4: "Wizz Air",
  W9: "Wizz Air",
  A9: "Georgian Airways",
  J2: "AZAL Azerbaijan",
  B2: "Belavia",
  KC: "Air Astana",
  DV: "SCAT Airlines",
  FZ: "flydubai",
  G9: "Air Arabia",
  EK: "Emirates",
  EY: "Etihad",
  QR: "Qatar Airways",
  HY: "Uzbekistan Airways",
  RC: "FlyArna",
  KK: "AtlasGlobal",
  XY: "flynas",
  SU: "Aeroflot",
  U6: "Ural Airlines",
  N4: "Nordwind",
  D8: "Norwegian",
};

export function airlineName(code: string | null): string {
  if (!code) return "—";
  return AIRLINES[code.toUpperCase()] ?? code;
}
