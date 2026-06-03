<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.show', ['version' => request()->route('version'), 'path' => 'api'])],
  ['label' => 'Organizations', 'route' => route('marketing.docs.show', ['version' => request()->route('version'), 'path' => 'api/organizations'])],
  ['label' => 'Branches'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Branches" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'list-branches',
        'title' => 'List all branches',
      ],
      [
        'id' => 'get-a-branch',
        'title' => 'Get a specific branch',
      ],
      [
        'id' => 'create-a-branch',
        'title' => 'Create a branch',
      ],
      [
        'id' => 'update-a-branch',
        'title' => 'Update a branch',
      ],
      [
        'id' => 'delete-a-branch',
        'title' => 'Delete a branch',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <p class="mb-2">Branches represent physical or virtual locations belonging to an organization (e.g. "Scranton Branch", "London HQ").</p>
        <p>
          All branch endpoints require you to be a member of the organization. Create, update, and delete operations additionally require the
          <strong>branch.manage</strong>
          permission.
        </p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#list-branches">
              <span class="text-blue-700">GET</span>
              /api/organizations/{id}/adminland/branches
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#get-a-branch">
              <span class="text-blue-700">GET</span>
              /api/organizations/{id}/adminland/branches/{branchId}
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#create-a-branch">
              <span class="text-green-700">POST</span>
              /api/organizations/{id}/adminland/branches
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#update-a-branch">
              <span class="text-yellow-700">PUT</span>
              /api/organizations/{id}/adminland/branches/{branchId}
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#delete-a-branch">
              <span class="text-red-700">DELETE</span>
              /api/organizations/{id}/adminland/branches/{branchId}
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- GET /api/organizations/{id}/adminland/branches -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="list-branches" title="List all branches" />
        <p class="mb-2">This endpoint returns all branches belonging to the given organization, ordered alphabetically by name.</p>
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
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource. Always 'branch'." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the branch." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the branch." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The name of the branch." />
          <x-marketing.docs.attribute name="attributes.slug" type="string" description="The URL-friendly identifier of the branch. Can be null." />
          <x-marketing.docs.attribute name="attributes.code" type="string" description="The branch code (e.g. 'HQ-001'). Can be null." />
          <x-marketing.docs.attribute name="attributes.description" type="string" description="A short description of the branch. Can be null." />
          <x-marketing.docs.attribute name="attributes.address_line_1" type="string" description="The first line of the branch address." />
          <x-marketing.docs.attribute name="attributes.address_line_2" type="string" description="The second line of the branch address. Can be null." />
          <x-marketing.docs.attribute name="attributes.city" type="string" description="The city the branch is located in." />
          <x-marketing.docs.attribute name="attributes.state_province" type="string" description="The state or province the branch is located in. Can be null." />
          <x-marketing.docs.attribute name="attributes.postal_code" type="string" description="The postal code of the branch. Can be null." />
          <x-marketing.docs.attribute name="attributes.timezone" type="string" description="The timezone of the branch (e.g. 'America/New_York'). Can be null." />
          <x-marketing.docs.attribute name="attributes.country_id" type="integer" description="The ID of the country. Can be null." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the branch." />
          <x-marketing.docs.attribute name="links.self" type="string" description="The URL of the branch." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/branches" verb="GET">
          <x-marketing.docs.json-section level="1" name="data">
            <x-marketing.docs.json-line level="2" key="type" value="branch" type="string" comma />
            <x-marketing.docs.json-line level="2" key="id" value="1" type="string" comma />
            <x-marketing.docs.json-section level="2" name="attributes" comma>
              <x-marketing.docs.json-line level="3" key="name" value="Scranton Branch" type="string" comma />
              <x-marketing.docs.json-line level="3" key="slug" value="1-scranton-branch" type="string" comma />
              <x-marketing.docs.json-line level="3" key="code" value="HQ-001" type="string" comma />
              <x-marketing.docs.json-line level="3" key="description" value="Main branch in Scranton" type="string" comma />
              <x-marketing.docs.json-line level="3" key="address_line_1" value="1725 Slough Avenue" type="string" comma />
              <x-marketing.docs.json-line level="3" key="address_line_2" value="Suite 200" type="string" comma />
              <x-marketing.docs.json-line level="3" key="city" value="Scranton" type="string" comma />
              <x-marketing.docs.json-line level="3" key="state_province" value="Pennsylvania" type="string" comma />
              <x-marketing.docs.json-line level="3" key="postal_code" value="18505" type="string" comma />
              <x-marketing.docs.json-line level="3" key="timezone" value="America/New_York" type="string" comma />
              <x-marketing.docs.json-line level="3" key="country_id" value="1" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="created_at" value="1771898698" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="updated_at" value="1771898698" type="integer" />
            </x-marketing.docs.json-section>
            <x-marketing.docs.json-section level="2" name="links">
              <x-marketing.docs.json-line level="3" key="self" value="http://libraryOS.test/api/organizations/1/adminland/branches/1" type="string" />
            </x-marketing.docs.json-section>
          </x-marketing.docs.json-section>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- GET /api/organizations/{id}/adminland/branches/{branchId} -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="get-a-branch" title="Get a specific branch" />
        <p class="mb-2">This endpoint returns a specific branch belonging to the given organization.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          any member of the organization.
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
          <x-marketing.docs.attribute required name="branchId" type="integer" description="The ID of the branch." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters no-parameters></x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource. Always 'branch'." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the branch." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the branch." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The name of the branch." />
          <x-marketing.docs.attribute name="attributes.slug" type="string" description="The URL-friendly identifier of the branch. Can be null." />
          <x-marketing.docs.attribute name="attributes.code" type="string" description="The branch code. Can be null." />
          <x-marketing.docs.attribute name="attributes.description" type="string" description="A short description of the branch. Can be null." />
          <x-marketing.docs.attribute name="attributes.address_line_1" type="string" description="The first line of the branch address." />
          <x-marketing.docs.attribute name="attributes.address_line_2" type="string" description="The second line of the branch address. Can be null." />
          <x-marketing.docs.attribute name="attributes.city" type="string" description="The city the branch is located in." />
          <x-marketing.docs.attribute name="attributes.state_province" type="string" description="The state or province. Can be null." />
          <x-marketing.docs.attribute name="attributes.postal_code" type="string" description="The postal code. Can be null." />
          <x-marketing.docs.attribute name="attributes.timezone" type="string" description="The timezone of the branch. Can be null." />
          <x-marketing.docs.attribute name="attributes.country_id" type="integer" description="The ID of the country. Can be null." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the branch." />
          <x-marketing.docs.attribute name="links.self" type="string" description="The URL of the branch." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/branches/{branchId}" verb="GET">
          <x-marketing.docs.json-section level="1" name="data">
            <x-marketing.docs.json-line level="2" key="type" value="branch" type="string" comma />
            <x-marketing.docs.json-line level="2" key="id" value="1" type="string" comma />
            <x-marketing.docs.json-section level="2" name="attributes" comma>
              <x-marketing.docs.json-line level="3" key="name" value="Scranton Branch" type="string" comma />
              <x-marketing.docs.json-line level="3" key="slug" value="1-scranton-branch" type="string" comma />
              <x-marketing.docs.json-line level="3" key="code" value="HQ-001" type="string" comma />
              <x-marketing.docs.json-line level="3" key="description" value="Main branch in Scranton" type="string" comma />
              <x-marketing.docs.json-line level="3" key="address_line_1" value="1725 Slough Avenue" type="string" comma />
              <x-marketing.docs.json-line level="3" key="address_line_2" value="Suite 200" type="string" comma />
              <x-marketing.docs.json-line level="3" key="city" value="Scranton" type="string" comma />
              <x-marketing.docs.json-line level="3" key="state_province" value="Pennsylvania" type="string" comma />
              <x-marketing.docs.json-line level="3" key="postal_code" value="18505" type="string" comma />
              <x-marketing.docs.json-line level="3" key="timezone" value="America/New_York" type="string" comma />
              <x-marketing.docs.json-line level="3" key="country_id" value="1" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="created_at" value="1771898698" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="updated_at" value="1771898698" type="integer" />
            </x-marketing.docs.json-section>
            <x-marketing.docs.json-section level="2" name="links">
              <x-marketing.docs.json-line level="3" key="self" value="http://libraryOS.test/api/organizations/1/adminland/branches/1" type="string" />
            </x-marketing.docs.json-section>
          </x-marketing.docs.json-section>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- POST /api/organizations/{id}/adminland/branches -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="create-a-branch" title="Create a branch" />
        <p class="mb-2">This endpoint creates a new branch for the given organization.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          branch.manage
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="name" type="string" description="The name of the branch. Maximum 100 characters." />
          <x-marketing.docs.attribute required name="address_line_1" type="string" description="The first line of the branch address. Maximum 100 characters." />
          <x-marketing.docs.attribute required name="city" type="string" description="The city of the branch. Maximum 100 characters." />
          <x-marketing.docs.attribute name="code" type="string" description="A short branch code (e.g. 'HQ-001'). Maximum 100 characters." />
          <x-marketing.docs.attribute name="description" type="string" description="A short description of the branch. Maximum 255 characters." />
          <x-marketing.docs.attribute name="address_line_2" type="string" description="The second line of the branch address. Maximum 100 characters." />
          <x-marketing.docs.attribute name="state_province" type="string" description="The state or province of the branch. Maximum 100 characters." />
          <x-marketing.docs.attribute name="postal_code" type="string" description="The postal code of the branch. Maximum 20 characters." />
          <x-marketing.docs.attribute name="timezone" type="string" description="The timezone of the branch (e.g. 'America/New_York'). Maximum 50 characters." />
          <x-marketing.docs.attribute name="country_id" type="integer" description="The ID of the country the branch is located in." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource. Always 'branch'." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the newly created branch." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the branch." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The name of the branch." />
          <x-marketing.docs.attribute name="attributes.slug" type="string" description="The URL-friendly identifier of the branch. Can be null." />
          <x-marketing.docs.attribute name="attributes.code" type="string" description="The branch code. Can be null." />
          <x-marketing.docs.attribute name="attributes.description" type="string" description="A short description of the branch. Can be null." />
          <x-marketing.docs.attribute name="attributes.address_line_1" type="string" description="The first line of the branch address." />
          <x-marketing.docs.attribute name="attributes.address_line_2" type="string" description="The second line of the branch address. Can be null." />
          <x-marketing.docs.attribute name="attributes.city" type="string" description="The city the branch is located in." />
          <x-marketing.docs.attribute name="attributes.state_province" type="string" description="The state or province. Can be null." />
          <x-marketing.docs.attribute name="attributes.postal_code" type="string" description="The postal code. Can be null." />
          <x-marketing.docs.attribute name="attributes.timezone" type="string" description="The timezone of the branch. Can be null." />
          <x-marketing.docs.attribute name="attributes.country_id" type="integer" description="The ID of the country. Can be null." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the branch." />
          <x-marketing.docs.attribute name="links.self" type="string" description="The URL of the branch." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/branches" verb="POST">
          <x-marketing.docs.json-section level="1" name="data">
            <x-marketing.docs.json-line level="2" key="type" value="branch" type="string" comma />
            <x-marketing.docs.json-line level="2" key="id" value="1" type="string" comma />
            <x-marketing.docs.json-section level="2" name="attributes" comma>
              <x-marketing.docs.json-line level="3" key="name" value="Scranton Branch" type="string" comma />
              <x-marketing.docs.json-line level="3" key="slug" value="1-scranton-branch" type="string" comma />
              <x-marketing.docs.json-line level="3" key="code" value="HQ-001" type="string" comma />
              <x-marketing.docs.json-line level="3" key="description" value="Main branch in Scranton" type="string" comma />
              <x-marketing.docs.json-line level="3" key="address_line_1" value="1725 Slough Avenue" type="string" comma />
              <x-marketing.docs.json-line level="3" key="address_line_2" value="Suite 200" type="string" comma />
              <x-marketing.docs.json-line level="3" key="city" value="Scranton" type="string" comma />
              <x-marketing.docs.json-line level="3" key="state_province" value="Pennsylvania" type="string" comma />
              <x-marketing.docs.json-line level="3" key="postal_code" value="18505" type="string" comma />
              <x-marketing.docs.json-line level="3" key="timezone" value="America/New_York" type="string" comma />
              <x-marketing.docs.json-line level="3" key="country_id" value="1" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="created_at" value="1771898698" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="updated_at" value="1771898698" type="integer" />
            </x-marketing.docs.json-section>
            <x-marketing.docs.json-section level="2" name="links">
              <x-marketing.docs.json-line level="3" key="self" value="http://libraryOS.test/api/organizations/1/adminland/branches/1" type="string" />
            </x-marketing.docs.json-section>
          </x-marketing.docs.json-section>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/organizations/{id}/adminland/branches/{branchId} -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="update-a-branch" title="Update a branch" />
        <p class="mb-2">This endpoint updates an existing branch.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          branch.manage
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
          <x-marketing.docs.attribute required name="branchId" type="integer" description="The ID of the branch to update." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="name" type="string" description="The name of the branch. Maximum 100 characters." />
          <x-marketing.docs.attribute required name="address_line_1" type="string" description="The first line of the branch address. Maximum 100 characters." />
          <x-marketing.docs.attribute required name="city" type="string" description="The city of the branch. Maximum 100 characters." />
          <x-marketing.docs.attribute name="code" type="string" description="A short branch code (e.g. 'HQ-001'). Maximum 100 characters." />
          <x-marketing.docs.attribute name="description" type="string" description="A short description of the branch. Maximum 255 characters." />
          <x-marketing.docs.attribute name="address_line_2" type="string" description="The second line of the branch address. Maximum 100 characters." />
          <x-marketing.docs.attribute name="state_province" type="string" description="The state or province of the branch. Maximum 100 characters." />
          <x-marketing.docs.attribute name="postal_code" type="string" description="The postal code of the branch. Maximum 20 characters." />
          <x-marketing.docs.attribute name="timezone" type="string" description="The timezone of the branch. Maximum 50 characters." />
          <x-marketing.docs.attribute name="country_id" type="integer" description="The ID of the country the branch is located in." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource. Always 'branch'." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the branch." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the branch." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The updated name of the branch." />
          <x-marketing.docs.attribute name="attributes.slug" type="string" description="The URL-friendly identifier of the branch. Can be null." />
          <x-marketing.docs.attribute name="attributes.code" type="string" description="The updated branch code. Can be null." />
          <x-marketing.docs.attribute name="attributes.description" type="string" description="The updated description. Can be null." />
          <x-marketing.docs.attribute name="attributes.address_line_1" type="string" description="The updated first line of the branch address." />
          <x-marketing.docs.attribute name="attributes.address_line_2" type="string" description="The updated second line of the branch address. Can be null." />
          <x-marketing.docs.attribute name="attributes.city" type="string" description="The updated city." />
          <x-marketing.docs.attribute name="attributes.state_province" type="string" description="The updated state or province. Can be null." />
          <x-marketing.docs.attribute name="attributes.postal_code" type="string" description="The updated postal code. Can be null." />
          <x-marketing.docs.attribute name="attributes.timezone" type="string" description="The updated timezone. Can be null." />
          <x-marketing.docs.attribute name="attributes.country_id" type="integer" description="The updated country ID. Can be null." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the branch." />
          <x-marketing.docs.attribute name="links.self" type="string" description="The URL of the branch." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/branches/{branchId}" verb="PUT">
          <x-marketing.docs.json-section level="1" name="data">
            <x-marketing.docs.json-line level="2" key="type" value="branch" type="string" comma />
            <x-marketing.docs.json-line level="2" key="id" value="1" type="string" comma />
            <x-marketing.docs.json-section level="2" name="attributes" comma>
              <x-marketing.docs.json-line level="3" key="name" value="Scranton Branch Updated" type="string" comma />
              <x-marketing.docs.json-line level="3" key="slug" value="1-scranton-branch-updated" type="string" comma />
              <x-marketing.docs.json-line level="3" key="code" value="HQ-001" type="string" comma />
              <x-marketing.docs.json-line level="3" key="description" value="Main branch in Scranton" type="string" comma />
              <x-marketing.docs.json-line level="3" key="address_line_1" value="1725 Slough Avenue" type="string" comma />
              <x-marketing.docs.json-line level="3" key="address_line_2" value="Suite 200" type="string" comma />
              <x-marketing.docs.json-line level="3" key="city" value="Scranton" type="string" comma />
              <x-marketing.docs.json-line level="3" key="state_province" value="Pennsylvania" type="string" comma />
              <x-marketing.docs.json-line level="3" key="postal_code" value="18505" type="string" comma />
              <x-marketing.docs.json-line level="3" key="timezone" value="America/New_York" type="string" comma />
              <x-marketing.docs.json-line level="3" key="country_id" value="1" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="created_at" value="1771898698" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="updated_at" value="1771898699" type="integer" />
            </x-marketing.docs.json-section>
            <x-marketing.docs.json-section level="2" name="links">
              <x-marketing.docs.json-line level="3" key="self" value="http://libraryOS.test/api/organizations/1/adminland/branches/1" type="string" />
            </x-marketing.docs.json-section>
          </x-marketing.docs.json-section>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- DELETE /api/organizations/{id}/adminland/branches/{branchId} -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="delete-a-branch" title="Delete a branch" />
        <p class="mb-2">This endpoint permanently deletes a branch from the organization.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          branch.manage
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
          <x-marketing.docs.attribute required name="branchId" type="integer" description="The ID of the branch to delete." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters no-parameters></x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes no-parameters></x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/branches/{branchId}" verb="DELETE">
          <div>No response body</div>
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
