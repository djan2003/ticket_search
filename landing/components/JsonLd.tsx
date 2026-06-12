// Renders a JSON-LD structured-data block. Kept tiny and shared so pages
// just pass a schema object built by lib/seo.ts.
export function JsonLd({ data }: { data: object }) {
  return (
    <script
      type="application/ld+json"
      dangerouslySetInnerHTML={{ __html: JSON.stringify(data) }}
    />
  );
}
