<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.show', ['version' => request()->route('version'), 'path' => 'api'])],
  ['label' => 'Organizations', 'route' => route('marketing.docs.show', ['version' => request()->route('version'), 'path' => 'api/organizations'])],
  ['label' => 'Item Types'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Item Types" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'list-item-types',
        'title' => 'List all item types',
      ],
      [
        'id' => 'get-an-item-type',
        'title' => 'Get a specific item type',
      ],
      [
        'id' => 'create-an-item-type',
        'title' => 'Create an item type',
      ],
      [
        'id' => 'update-an-item-type',
        'title' => 'Update an item type',
      ],
      [
        'id' => 'delete-an-item-type',
        'title' => 'Delete an item type',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <p class="mb-2">Item types categorize the materials in your collection and control how they behave in the circulation workflow (e.g. "Book", "DVD", "Magazine").</p>
        <p>
          All item type endpoints require you to be a member of the organization. Create, update, and delete operations additionally require the
          <strong>item_type.manage</strong>
          permission.
        </p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#list-item-types">
              <span class="text-blue-700">GET</span>
              /api/organizations/{id}/adminland/item-types
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#get-an-item-type">
              <span class="text-blue-700">GET</span>
              /api/organizations/{id}/adminland/item-types/{itemTypeId}
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#create-an-item-type">
              <span class="text-green-700">POST</span>
              /api/organizations/{id}/adminland/item-types
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#update-an-item-type">
              <span class="text-yellow-700">PUT</span>
              /api/organizations/{id}/adminland/item-types/{itemTypeId}
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#delete-an-item-type">
              <span class="text-red-700">DELETE</span>
              /api/organizations/{id}/adminland/item-types/{itemTypeId}
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- GET /api/organizations/{id}/adminland/item-types -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="list-item-types" title="List all item types" />
        <p class="mb-2">This endpoint returns all item types belonging to the given organization, ordered alphabetically by name.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          any member of the organization. This call is not
          <x-link href="{{ route('marketing.docs.show', ['version' => request()->route('version'), 'path' => 'api']) }}#pagination">paginated</x-link>
          at the moment.
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters no-parameters></x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource. Always 'item_type'." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the item type." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the item type." />
          <x-marketing.docs.attribute name="attributes.key" type="string" description="The unique key identifying this item type (e.g. 'book')." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The display name of the item type. Can be null." />
          <x-marketing.docs.attribute name="attributes.description" type="string" description="A short description of the item type. Can be null." />
          <x-marketing.docs.attribute name="attributes.is_loanable" type="boolean" description="Whether items of this type can be checked out to patrons." />
          <x-marketing.docs.attribute name="attributes.is_holdable" type="boolean" description="Whether patrons can place holds on items of this type." />
          <x-marketing.docs.attribute name="attributes.is_visible_in_catalog" type="boolean" description="Whether items of this type appear in the public catalog." />
          <x-marketing.docs.attribute name="attributes.default_loan_days" type="integer" description="The default number of days for a loan of this type. Can be null." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the item type." />
          <x-marketing.docs.attribute name="links.self" type="string" description="The URL of the item type." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/item-types" verb="GET">
          <x-marketing.docs.json-section level="1" name="data">
            <x-marketing.docs.json-line level="2" key="type" value="item_type" type="string" comma />
            <x-marketing.docs.json-line level="2" key="id" value="1" type="string" comma />
            <x-marketing.docs.json-section level="2" name="attributes" comma>
              <x-marketing.docs.json-line level="3" key="key" value="book" type="string" comma />
              <x-marketing.docs.json-line level="3" key="name" value="Book" type="string" comma />
              <x-marketing.docs.json-line level="3" key="description" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_loanable" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_holdable" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_visible_in_catalog" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="default_loan_days" value="14" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="created_at" value="1771898698" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="updated_at" value="1771898698" type="integer" />
            </x-marketing.docs.json-section>
            <x-marketing.docs.json-section level="2" name="links">
              <x-marketing.docs.json-line level="3" key="self" value="http://libraryOS.test/api/organizations/1/adminland/item-types/1" type="string" />
            </x-marketing.docs.json-section>
          </x-marketing.docs.json-section>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- GET /api/organizations/{id}/adminland/item-types/{itemTypeId} -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="get-an-item-type" title="Get a specific item type" />
        <p class="mb-2">This endpoint returns a specific item type belonging to the given organization.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          any member of the organization.
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
          <x-marketing.docs.attribute required name="itemTypeId" type="integer" description="The ID of the item type." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters no-parameters></x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource. Always 'item_type'." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the item type." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the item type." />
          <x-marketing.docs.attribute name="attributes.key" type="string" description="The unique key identifying this item type." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The display name of the item type. Can be null." />
          <x-marketing.docs.attribute name="attributes.description" type="string" description="A short description. Can be null." />
          <x-marketing.docs.attribute name="attributes.is_loanable" type="boolean" description="Whether items of this type can be checked out to patrons." />
          <x-marketing.docs.attribute name="attributes.is_holdable" type="boolean" description="Whether patrons can place holds on items of this type." />
          <x-marketing.docs.attribute name="attributes.is_visible_in_catalog" type="boolean" description="Whether items of this type appear in the public catalog." />
          <x-marketing.docs.attribute name="attributes.default_loan_days" type="integer" description="The default loan duration in days. Can be null." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the item type." />
          <x-marketing.docs.attribute name="links.self" type="string" description="The URL of the item type." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/item-types/{itemTypeId}" verb="GET">
          <x-marketing.docs.json-section level="1" name="data">
            <x-marketing.docs.json-line level="2" key="type" value="item_type" type="string" comma />
            <x-marketing.docs.json-line level="2" key="id" value="1" type="string" comma />
            <x-marketing.docs.json-section level="2" name="attributes" comma>
              <x-marketing.docs.json-line level="3" key="key" value="book" type="string" comma />
              <x-marketing.docs.json-line level="3" key="name" value="Book" type="string" comma />
              <x-marketing.docs.json-line level="3" key="description" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_loanable" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_holdable" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_visible_in_catalog" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="default_loan_days" value="14" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="created_at" value="1771898698" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="updated_at" value="1771898698" type="integer" />
            </x-marketing.docs.json-section>
            <x-marketing.docs.json-section level="2" name="links">
              <x-marketing.docs.json-line level="3" key="self" value="http://libraryOS.test/api/organizations/1/adminland/item-types/1" type="string" />
            </x-marketing.docs.json-section>
          </x-marketing.docs.json-section>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- POST /api/organizations/{id}/adminland/item-types -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="create-an-item-type" title="Create an item type" />
        <p class="mb-2">This endpoint creates a new item type for the given organization.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          item_type.manage
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="key" type="string" description="A unique key identifying this item type (e.g. 'book'). Maximum 100 characters." />
          <x-marketing.docs.attribute name="name" type="string" description="The display name of the item type. Maximum 100 characters." />
          <x-marketing.docs.attribute name="description" type="string" description="A short description. Maximum 255 characters." />
          <x-marketing.docs.attribute name="is_loanable" type="boolean" description="Whether items of this type can be checked out. Defaults to true." />
          <x-marketing.docs.attribute name="is_holdable" type="boolean" description="Whether patrons can place holds on items of this type. Defaults to true." />
          <x-marketing.docs.attribute name="is_visible_in_catalog" type="boolean" description="Whether items appear in the public catalog. Defaults to true." />
          <x-marketing.docs.attribute name="default_loan_days" type="integer" description="The default loan duration in days. Must be at least 1." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource. Always 'item_type'." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the newly created item type." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the item type." />
          <x-marketing.docs.attribute name="attributes.key" type="string" description="The unique key." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The display name. Can be null." />
          <x-marketing.docs.attribute name="attributes.description" type="string" description="The description. Can be null." />
          <x-marketing.docs.attribute name="attributes.is_loanable" type="boolean" description="Whether items can be checked out." />
          <x-marketing.docs.attribute name="attributes.is_holdable" type="boolean" description="Whether holds are allowed." />
          <x-marketing.docs.attribute name="attributes.is_visible_in_catalog" type="boolean" description="Whether items appear in the catalog." />
          <x-marketing.docs.attribute name="attributes.default_loan_days" type="integer" description="The default loan duration. Can be null." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the item type." />
          <x-marketing.docs.attribute name="links.self" type="string" description="The URL of the item type." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/item-types" verb="POST">
          <x-marketing.docs.json-section level="1" name="data">
            <x-marketing.docs.json-line level="2" key="type" value="item_type" type="string" comma />
            <x-marketing.docs.json-line level="2" key="id" value="1" type="string" comma />
            <x-marketing.docs.json-section level="2" name="attributes" comma>
              <x-marketing.docs.json-line level="3" key="key" value="book" type="string" comma />
              <x-marketing.docs.json-line level="3" key="name" value="Book" type="string" comma />
              <x-marketing.docs.json-line level="3" key="description" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_loanable" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_holdable" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_visible_in_catalog" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="default_loan_days" value="14" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="created_at" value="1771898698" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="updated_at" value="1771898698" type="integer" />
            </x-marketing.docs.json-section>
            <x-marketing.docs.json-section level="2" name="links">
              <x-marketing.docs.json-line level="3" key="self" value="http://libraryOS.test/api/organizations/1/adminland/item-types/1" type="string" />
            </x-marketing.docs.json-section>
          </x-marketing.docs.json-section>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/organizations/{id}/adminland/item-types/{itemTypeId} -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="update-an-item-type" title="Update an item type" />
        <p class="mb-2">This endpoint updates an existing item type.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          item_type.manage
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
          <x-marketing.docs.attribute required name="itemTypeId" type="integer" description="The ID of the item type to update." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="key" type="string" description="A unique key identifying this item type. Maximum 100 characters." />
          <x-marketing.docs.attribute name="name" type="string" description="The display name of the item type. Maximum 100 characters." />
          <x-marketing.docs.attribute name="description" type="string" description="A short description. Maximum 255 characters." />
          <x-marketing.docs.attribute name="is_loanable" type="boolean" description="Whether items of this type can be checked out." />
          <x-marketing.docs.attribute name="is_holdable" type="boolean" description="Whether patrons can place holds on items of this type." />
          <x-marketing.docs.attribute name="is_visible_in_catalog" type="boolean" description="Whether items appear in the public catalog." />
          <x-marketing.docs.attribute name="default_loan_days" type="integer" description="The default loan duration in days. Must be at least 1." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource. Always 'item_type'." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the item type." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the item type." />
          <x-marketing.docs.attribute name="attributes.key" type="string" description="The updated key." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The updated name. Can be null." />
          <x-marketing.docs.attribute name="attributes.description" type="string" description="The updated description. Can be null." />
          <x-marketing.docs.attribute name="attributes.is_loanable" type="boolean" description="Updated loanable flag." />
          <x-marketing.docs.attribute name="attributes.is_holdable" type="boolean" description="Updated holdable flag." />
          <x-marketing.docs.attribute name="attributes.is_visible_in_catalog" type="boolean" description="Updated catalog visibility flag." />
          <x-marketing.docs.attribute name="attributes.default_loan_days" type="integer" description="Updated default loan days. Can be null." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the item type." />
          <x-marketing.docs.attribute name="links.self" type="string" description="The URL of the item type." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/item-types/{itemTypeId}" verb="PUT">
          <x-marketing.docs.json-section level="1" name="data">
            <x-marketing.docs.json-line level="2" key="type" value="item_type" type="string" comma />
            <x-marketing.docs.json-line level="2" key="id" value="1" type="string" comma />
            <x-marketing.docs.json-section level="2" name="attributes" comma>
              <x-marketing.docs.json-line level="3" key="key" value="book-updated" type="string" comma />
              <x-marketing.docs.json-line level="3" key="name" value="Book Updated" type="string" comma />
              <x-marketing.docs.json-line level="3" key="description" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_loanable" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_holdable" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_visible_in_catalog" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="default_loan_days" value="14" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="created_at" value="1771898698" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="updated_at" value="1771898699" type="integer" />
            </x-marketing.docs.json-section>
            <x-marketing.docs.json-section level="2" name="links">
              <x-marketing.docs.json-line level="3" key="self" value="http://libraryOS.test/api/organizations/1/adminland/item-types/1" type="string" />
            </x-marketing.docs.json-section>
          </x-marketing.docs.json-section>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- DELETE /api/organizations/{id}/adminland/item-types/{itemTypeId} -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="delete-an-item-type" title="Delete an item type" />
        <p class="mb-2">This endpoint permanently deletes an item type from the organization.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          item_type.manage
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
          <x-marketing.docs.attribute required name="itemTypeId" type="integer" description="The ID of the item type to delete." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters no-parameters></x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes no-parameters></x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/item-types/{itemTypeId}" verb="DELETE">
          <div>No response body</div>
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
