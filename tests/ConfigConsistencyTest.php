<?php

namespace Tests;

class ConfigConsistencyTest extends TestCase
{
    private function landing(): string
    {
        return file_get_contents(base_path('landing/lib/destinations.ts'));
    }

    public function test_every_origin_has_a_visa_free_list_and_a_passport_name()
    {
        foreach (config('airports.origins') as $code => $origin) {
            $this->assertNotEmpty(config("countries.visa_free.{$origin['passport']}"), "{$code}: no visa_free list");
            $this->assertNotEmpty(config("countries.passports.{$origin['passport']}"), "{$code}: no passport name");
        }
    }

    public function test_every_configured_destination_has_a_landing_page_for_that_passport()
    {
        preg_match_all('/iata: "([A-Z]{3})".*?passports: \[([^\]]*)\]/', $this->landing(), $m, PREG_SET_ORDER);
        $landing = [];
        foreach ($m as $row) {
            preg_match_all('/"([A-Z]{2})"/', $row[2], $p);
            $landing[$row[1]] = $p[1];
        }

        foreach (config('countries.visa_free') as $passport => $countries) {
            foreach ($countries as $country) {
                foreach ($country['cities'] as $city) {
                    $this->assertContains($passport, $landing[$city] ?? [], "{$city} has no landing page for {$passport}");
                }
            }
        }
    }

    public function test_every_origin_is_known_to_the_landing()
    {
        foreach (config('airports.origins') as $code => $origin) {
            $this->assertStringContainsString("iata: \"{$code}\", slug: \"{$origin['slug']}\"", $this->landing(), $code);
        }
    }
}
