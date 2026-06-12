import Script from "next/script";
import { SITE } from "../lib/site";

// Renders the Umami tracking script only when both env vars are present,
// so the landing still builds/runs before analytics is configured.
export function Analytics() {
  if (!SITE.umamiSrc || !SITE.umamiWebsiteId) return null;
  return (
    <Script
      src={SITE.umamiSrc}
      data-website-id={SITE.umamiWebsiteId}
      strategy="afterInteractive"
      defer
    />
  );
}
