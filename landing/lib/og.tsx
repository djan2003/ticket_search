import { ImageResponse } from "next/og";
import { readFileSync } from "fs";
import { join } from "path";

// Shared Open Graph image renderer used by the per-route opengraph-image
// files. All OG routes are statically prerendered at build time, so the
// fonts are read from disk once at module load. PT Sans covers Cyrillic +
// Latin (the default next/og font does not render Cyrillic).
export const OG_SIZE = { width: 1200, height: 630 };
export const OG_CONTENT_TYPE = "image/png";

const fontDir = join(process.cwd(), "lib", "og-fonts");
const fontBold = readFileSync(join(fontDir, "PTSans-Bold.ttf"));
const fontRegular = readFileSync(join(fontDir, "PTSans-Regular.ttf"));

const fonts = [
  { name: "PT Sans", data: fontBold, weight: 700 as const, style: "normal" as const },
  { name: "PT Sans", data: fontRegular, weight: 400 as const, style: "normal" as const },
];

export function renderOgImage(title: string, subtitle: string) {
  return new ImageResponse(
    (
      <div
        style={{
          width: "100%",
          height: "100%",
          display: "flex",
          flexDirection: "column",
          justifyContent: "space-between",
          padding: "72px 80px",
          background: "linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%)",
          fontFamily: "PT Sans",
          color: "white",
        }}
      >
        <div style={{ fontSize: 36, fontWeight: 700, letterSpacing: 1 }}>
          flykend.com
        </div>

        <div style={{ display: "flex", flexDirection: "column", gap: 20 }}>
          <div style={{ fontSize: 76, fontWeight: 700, lineHeight: 1.1 }}>
            {title}
          </div>
          <div style={{ fontSize: 40, fontWeight: 400, color: "#e0f2fe" }}>
            {subtitle}
          </div>
        </div>

        <div style={{ fontSize: 30, fontWeight: 400, color: "#bae6fd" }}>
          Дешёвые авиабилеты на выходные · лучшие цены каждый день в Telegram
        </div>
      </div>
    ),
    { ...OG_SIZE, fonts },
  );
}
