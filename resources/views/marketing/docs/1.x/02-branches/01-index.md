# Offices

## Overview

An office is a physical or virtual location where your [organization](/docs/1.x/organizations) operates. Offices are organized using office types — reusable labels that classify what kind of location each office is.

To learn how to create, edit, and delete offices and office types, see [Manage offices](/docs/1.x/offices/manage).

## Office types

An office type is a label that describes the nature of a location. Common examples include **Headquarters**, **Branch**, **Warehouse**, and **Co-working Space**.

Office types are simple, reusable tags. You define the list once in Adminland, then assign a type to each office to keep your locations organized.

Consider Dunder Mifflin Paper Company. They run several kinds of locations across the United States:

- **Branch** — regional sales offices like Scranton, Stamford, and Utica
- **Headquarters** — the corporate office in New York
- **Warehouse** — distribution centers that hold paper stock

Each office is tagged with one of these types. Anyone looking at the office list can immediately see what role a location plays in the company.

An office type is optional on an office. Deleting an office type does not delete associated offices — those offices simply lose their classification and become untyped.

## Offices

An office can represent anything from a corporate building with a precise street address to a country-level entry for a fully remote team. Each office stores:

- **Name** — a human-readable label, such as _Scranton Branch_ or _New York HQ_.
- **Address** — up to two address lines, city, state or province, and postal code.
- **Country** — the country the office is located in.
- **Time zone** — the IANA time zone identifier for the office (for example, `America/New_York` or `Europe/London`). This lets the rest of the organization know what local time applies to that location.
- **Office type** — an optional classification label.

Only the name, the first address line, and the city are required. Everything else is optional, so you can create a minimal office record and fill in the details later.

Consider Dunder Mifflin Paper Company's locations:

- **Scranton Branch** — 1725 Slough Avenue, Scranton, PA 18505, America/New_York
- **Stamford Branch** — 200 Connecticut Ave, Stamford, CT 06902, America/New_York
- **New York Headquarters** — 99 Park Avenue, New York, NY 10016, America/New_York

Offices belong to a single organization and are not shared across organizations.
