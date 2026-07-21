---
id: reference.fieldAndStatus
title: Field and status reference
slug: field-and-status-reference
section: reference
---

# Field and status reference

Every option set you meet in a KolleK form, in one scannable place. Each group links to the guide that uses it. For definitions of the terms themselves, see the @doc(reference.glossary, "glossary").

## Copy statuses

Set on each copy you record. Used in @doc(copies.track).

| Status | Meaning |
| --- | --- |
| Owned | You hold this copy. The default for a new copy. |
| Ordered | Bought or reserved, on its way to you. |
| Loaned out | With someone else for now. Custody moved, ownership did not. |
| Sold | You sold it and no longer own it. |
| Gifted | You gave it away. |
| Lost | You cannot find it and do not expect to. |
| Stolen | Taken from you. |
| Disposed | Discarded or recycled, with an optional disposed date. |
| Other | Anything the list above does not cover. |

:::note
Owned, Ordered, and Loaned out count as still held. A loaned copy is still yours, it is just somewhere else.
:::

## Transaction types

Set on each transaction. Used in @doc(copies.recordPaymentsAndValue). Types marked acquiring bring a copy into your hands, and the earliest acquiring transaction provides the copy's acquisition date.

| Type | Meaning |
| --- | --- |
| Purchase | You bought the copy. Acquiring. |
| Sale | You sold the copy. |
| Trade | You swapped something for it. Acquiring. |
| Gift received | Someone gave it to you. Acquiring. |
| Gift given | You gave it to someone. |
| Inheritance | It passed to you. Acquiring. |
| Refund | Money returned on an earlier transaction. |
| Fee | A cost around the copy, such as an auction fee. |
| Tax | A tax paid on the copy. |
| Shipping | A delivery cost recorded on its own. |
| Other | Any money event the list does not cover. |

## Valuation types and confidence

Set on each valuation. Used in @doc(copies.recordPaymentsAndValue).

| Valuation type | Meaning |
| --- | --- |
| Own estimate | Your own judgement of the value. |
| Professional appraisal | A formal appraisal by a professional. |
| Market estimate | Derived from current market or sales data. |
| Insurance value | The value used for insurance purposes. |
| Auction estimate | An estimate given by an auction house. |
| Automated estimate | Produced by a pricing service or tool. |
| Other | Any other basis for the value. |

| Confidence | Meaning |
| --- | --- |
| Low | A rough guess. |
| Medium | Reasonably grounded. |
| High | Well supported, such as a recent professional appraisal. |
| Unknown | Confidence was not recorded. |

## Insurance statuses

Set on each insurance record. Used in @doc(copies.insure). The coverage type on an insurance record is free text, so it has no fixed option list.

| Status | Meaning |
| --- | --- |
| Active | The policy currently covers the copy. |
| Expired | The coverage period has ended. |
| Cancelled | The policy was cancelled before its end date. |
| Pending | Coverage is arranged but not yet in force. |

## Loan directions and statuses

Set on each loan. Used in @doc(loans.lendAndBorrow).

| Direction | Meaning |
| --- | --- |
| Lent out | Your copy left your hands, for example to a friend or an exhibition. |
| Borrowed in | Someone else's piece is in your hands. |

| Status | Meaning |
| --- | --- |
| Planned | Agreed but not yet handed over. |
| Active | The copy is currently out (or in). |
| Overdue | Still out past its due date. KolleK flags this automatically each day. |
| Returned | The loan ended and the copy came back. |
| Cancelled | The loan never happened. |
| Lost | The copy did not come back. |

## Maintenance types

Set on each maintenance record. Used in @doc(copies.recordMaintenance).

| Type | Meaning |
| --- | --- |
| Cleaning | Routine cleaning. |
| Repair | Fixing damage. |
| Servicing | Periodic upkeep, such as a watch service. |
| Conservation | Work to stabilize and preserve. |
| Restoration | Work to return the copy to an earlier state. |
| Replacement | Replacing a part or component. |
| Inspection | A check without intervention. |

## Provenance event types and date precision

Set on each provenance event. Used in @doc(copies.traceProvenance).

| Event type | Meaning |
| --- | --- |
| Acquisition | The copy entered a collection. |
| Sale | The copy was sold. |
| Gift | The copy changed hands as a gift. |
| Inheritance | The copy passed through an estate. |
| Ownership transfer | Ownership changed in another way. |
| Custody transfer | The copy moved without changing owner. |
| Loan | The copy went out on loan. |
| Return | The copy came back from a loan. |
| Exhibition | The copy was shown publicly. |
| Authentication | The copy was verified as genuine. |
| Appraisal | The copy was formally valued. |
| Significant restoration | Major work that belongs in the story. |
| Origin | Where and when the copy was made. |
| Discovery | The copy was found or rediscovered. |
| Other | Any other chapter in the story. |

Provenance dates are often uncertain, so each event carries a precision:

| Precision | Meaning |
| --- | --- |
| Exact date | The full date is known. |
| Month | Known to the month. |
| Year | Known to the year. |
| Approximate | A best estimate. Read it as circa. |
| Unknown | No date is recorded. |

## Document types

Set on each document. Used in @doc(copies.attachDocuments).

| Type | Meaning |
| --- | --- |
| Receipt | Proof of a purchase. |
| Invoice | A bill for the copy or work on it. |
| Certificate | A certificate that came with the copy. |
| Appraisal | A written valuation. |
| Insurance | Policy paperwork. |
| Photograph | A photo kept as a record rather than a gallery image. |
| Condition report | A written assessment of condition. |
| Restoration report | A record of restoration work. |
| Catalogue | A catalogue entry or listing. |
| Correspondence | Letters or emails about the copy. |
| Ownership record | Paperwork proving ownership. |
| Authenticity record | Paperwork proving the copy is genuine. |
| Other | Anything else worth keeping. |

## Custom field types

Chosen when defining a custom field on a collection type. Used in @doc(collectionTypes.setup).

| Field type | Meaning |
| --- | --- |
| Text | Free text, such as an author or a publisher. |
| Number | A numeric value, such as an issue number. |
| Date | A calendar date, such as a release date. |
| Yes / No | A checkbox, such as "Signed". |
| Select | One choice from a list of options you define. |
| Rating | A star rating, up to five stars. |

## Collection visibility

Set on each collection. Used in @doc(collections.share). The setting is recorded today and enforced once sharing arrives; see @doc(troubleshooting.featureStatus).

| Visibility | Meaning |
| --- | --- |
| Private | Meant for you alone. |
| Shared | Meant for everyone in your account. |
| Public | Meant for anyone with the link, read only, without signing in. |

## Where to next

- What the terms mean: @doc(reference.glossary).
- The records these options live on: @doc(copyHistory.concept, "A copy's history explained").
