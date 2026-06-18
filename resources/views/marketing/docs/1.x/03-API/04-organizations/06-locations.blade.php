<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.show', ['version' => request()->route('version'), 'path' => 'api'])],
  ['label' => 'Organizations', 'route' => route('marketing.docs.show', ['version' => request()->route('version'), 'path' => 'api/organizations'])],
  ['label' => 'Locations'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Locations" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'list-locations',
        'title' => 'List all locations',
      ],
      [
        'id' => 'get-a-location',
        'title' => 'Get a specific location',
      ],
      [
        'id' => 'create-a-location',
        'title' => 'Create a location',
      ],
      [
        'id' => 'update-a-location',
        'title' => 'Update a location',
      ],
      [
        'id' => 'delete-a-location',
        'title' => 'Delete a location',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <p class="mb-2">Locations represent physical areas within a branch where items are shelved or stored (e.g. "Main Stacks", "Reference Room", "Children's Section").</p>
        <p>
          All location endpoints require you to be a member of the organization. Create, update, and delete operations additionally require the
          <strong>location.manage</strong>
          permission.
        </p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#list-locations">
              <span class="text-blue-700">GET</span>
              /api/organizations/{id}/adminland/locations
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#get-a-location">
              <span class="text-blue-700">GET</span>
              /api/organizations/{id}/adminland/locations/{locationId}
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#create-a-location">
              <span class="text-green-700">POST</span>
              /api/organizations/{id}/adminland/locations
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#update-a-location">
              <span class="text-yellow-700">PUT</span>
              /api/organizations/{id}/adminland/locations/{locationId}
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#delete-a-location">
              <span class="text-red-700">DELETE</span>
              /api/organizations/{id}/adminland/locations/{locationId}
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- GET /api/organizations/{id}/adminland/locations -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="list-locations" title="List all locations" />
        <p class="mb-2">This endpoint returns all locations belonging to the given organization, ordered alphabetically by name.</p>
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
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource. Always 'location'." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the location." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the location." />
          <x-marketing.docs.attribute name="attributes.organization_id" type="integer" description="The ID of the organization this location belongs to." />
          <x-marketing.docs.attribute name="attributes.branch_id" type="integer" description="The ID of the branch this location belongs to." />
          <x-marketing.docs.attribute name="attributes.parent_id" type="integer" description="The ID of the parent location. Can be null." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The name of the location." />
          <x-marketing.docs.attribute name="attributes.code" type="string" description="A short code identifying the location. Can be null." />
          <x-marketing.docs.attribute name="attributes.description" type="string" description="A description of the location. Can be null." />
          <x-marketing.docs.attribute name="attributes.is_active" type="boolean" description="Whether the location is active." />
          <x-marketing.docs.attribute name="attributes.is_public" type="boolean" description="Whether the location is visible to patrons." />
          <x-marketing.docs.attribute name="attributes.supports_pickups" type="boolean" description="Whether items can be picked up at this location." />
          <x-marketing.docs.attribute name="attributes.supports_returns" type="boolean" description="Whether items can be returned at this location." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the location." />
          <x-marketing.docs.attribute name="links.self" type="string" description="The URL of the location." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/locations" verb="GET">
          <x-marketing.docs.json-section level="1" name="data">
            <x-marketing.docs.json-line level="2" key="type" value="location" type="string" comma />
            <x-marketing.docs.json-line level="2" key="id" value="1" type="string" comma />
            <x-marketing.docs.json-section level="2" name="attributes" comma>
              <x-marketing.docs.json-line level="3" key="organization_id" value="1" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="branch_id" value="1" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="parent_id" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="name" value="Main Stacks" type="string" comma />
              <x-marketing.docs.json-line level="3" key="code" value="MS" type="string" comma />
              <x-marketing.docs.json-line level="3" key="description" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_active" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_public" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="supports_pickups" value="false" type="string" comma />
              <x-marketing.docs.json-line level="3" key="supports_returns" value="false" type="string" comma />
              <x-marketing.docs.json-line level="3" key="created_at" value="1771898698" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="updated_at" value="1771898698" type="integer" />
            </x-marketing.docs.json-section>
            <x-marketing.docs.json-section level="2" name="links">
              <x-marketing.docs.json-line level="3" key="self" value="http://libraryOS.test/api/organizations/1/adminland/locations/1" type="string" />
            </x-marketing.docs.json-section>
          </x-marketing.docs.json-section>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- GET /api/organizations/{id}/adminland/locations/{locationId} -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="get-a-location" title="Get a specific location" />
        <p class="mb-2">This endpoint returns a specific location belonging to the given organization.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          any member of the organization.
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
          <x-marketing.docs.attribute required name="locationId" type="integer" description="The ID of the location." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters no-parameters></x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource. Always 'location'." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the location." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the location." />
          <x-marketing.docs.attribute name="attributes.organization_id" type="integer" description="The ID of the organization." />
          <x-marketing.docs.attribute name="attributes.branch_id" type="integer" description="The ID of the branch." />
          <x-marketing.docs.attribute name="attributes.parent_id" type="integer" description="The ID of the parent location. Can be null." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The name of the location." />
          <x-marketing.docs.attribute name="attributes.code" type="string" description="A short code identifying the location. Can be null." />
          <x-marketing.docs.attribute name="attributes.description" type="string" description="A description of the location. Can be null." />
          <x-marketing.docs.attribute name="attributes.is_active" type="boolean" description="Whether the location is active." />
          <x-marketing.docs.attribute name="attributes.is_public" type="boolean" description="Whether the location is visible to patrons." />
          <x-marketing.docs.attribute name="attributes.supports_pickups" type="boolean" description="Whether items can be picked up at this location." />
          <x-marketing.docs.attribute name="attributes.supports_returns" type="boolean" description="Whether items can be returned at this location." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the location." />
          <x-marketing.docs.attribute name="links.self" type="string" description="The URL of the location." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/locations/{locationId}" verb="GET">
          <x-marketing.docs.json-section level="1" name="data">
            <x-marketing.docs.json-line level="2" key="type" value="location" type="string" comma />
            <x-marketing.docs.json-line level="2" key="id" value="1" type="string" comma />
            <x-marketing.docs.json-section level="2" name="attributes" comma>
              <x-marketing.docs.json-line level="3" key="organization_id" value="1" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="branch_id" value="1" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="parent_id" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="name" value="Main Stacks" type="string" comma />
              <x-marketing.docs.json-line level="3" key="code" value="MS" type="string" comma />
              <x-marketing.docs.json-line level="3" key="description" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_active" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_public" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="supports_pickups" value="false" type="string" comma />
              <x-marketing.docs.json-line level="3" key="supports_returns" value="false" type="string" comma />
              <x-marketing.docs.json-line level="3" key="created_at" value="1771898698" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="updated_at" value="1771898698" type="integer" />
            </x-marketing.docs.json-section>
            <x-marketing.docs.json-section level="2" name="links">
              <x-marketing.docs.json-line level="3" key="self" value="http://libraryOS.test/api/organizations/1/adminland/locations/1" type="string" />
            </x-marketing.docs.json-section>
          </x-marketing.docs.json-section>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- POST /api/organizations/{id}/adminland/locations -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="create-a-location" title="Create a location" />
        <p class="mb-2">This endpoint creates a new location for the given organization.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          location.manage
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="branch_id" type="integer" description="The ID of the branch this location belongs to." />
          <x-marketing.docs.attribute required name="name" type="string" description="The name of the location. Maximum 100 characters." />
          <x-marketing.docs.attribute name="parent_id" type="integer" description="The ID of a parent location for nested locations." />
          <x-marketing.docs.attribute name="code" type="string" description="A short code identifying the location. Maximum 50 characters." />
          <x-marketing.docs.attribute name="description" type="string" description="A description of the location. Maximum 255 characters." />
          <x-marketing.docs.attribute name="is_active" type="boolean" description="Whether the location is active. Defaults to true." />
          <x-marketing.docs.attribute name="is_public" type="boolean" description="Whether the location is visible to patrons. Defaults to true." />
          <x-marketing.docs.attribute name="supports_pickups" type="boolean" description="Whether items can be picked up at this location. Defaults to false." />
          <x-marketing.docs.attribute name="supports_returns" type="boolean" description="Whether items can be returned at this location. Defaults to false." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource. Always 'location'." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the newly created location." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the location." />
          <x-marketing.docs.attribute name="attributes.organization_id" type="integer" description="The ID of the organization." />
          <x-marketing.docs.attribute name="attributes.branch_id" type="integer" description="The ID of the branch." />
          <x-marketing.docs.attribute name="attributes.parent_id" type="integer" description="The ID of the parent location. Can be null." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The name of the location." />
          <x-marketing.docs.attribute name="attributes.code" type="string" description="The short code. Can be null." />
          <x-marketing.docs.attribute name="attributes.description" type="string" description="The description. Can be null." />
          <x-marketing.docs.attribute name="attributes.is_active" type="boolean" description="Whether the location is active." />
          <x-marketing.docs.attribute name="attributes.is_public" type="boolean" description="Whether the location is visible to patrons." />
          <x-marketing.docs.attribute name="attributes.supports_pickups" type="boolean" description="Whether items can be picked up at this location." />
          <x-marketing.docs.attribute name="attributes.supports_returns" type="boolean" description="Whether items can be returned at this location." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the location." />
          <x-marketing.docs.attribute name="links.self" type="string" description="The URL of the location." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/locations" verb="POST">
          <x-marketing.docs.json-section level="1" name="data">
            <x-marketing.docs.json-line level="2" key="type" value="location" type="string" comma />
            <x-marketing.docs.json-line level="2" key="id" value="1" type="string" comma />
            <x-marketing.docs.json-section level="2" name="attributes" comma>
              <x-marketing.docs.json-line level="3" key="organization_id" value="1" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="branch_id" value="1" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="parent_id" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="name" value="Main Stacks" type="string" comma />
              <x-marketing.docs.json-line level="3" key="code" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="description" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_active" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_public" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="supports_pickups" value="false" type="string" comma />
              <x-marketing.docs.json-line level="3" key="supports_returns" value="false" type="string" comma />
              <x-marketing.docs.json-line level="3" key="created_at" value="1771898698" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="updated_at" value="1771898698" type="integer" />
            </x-marketing.docs.json-section>
            <x-marketing.docs.json-section level="2" name="links">
              <x-marketing.docs.json-line level="3" key="self" value="http://libraryOS.test/api/organizations/1/adminland/locations/1" type="string" />
            </x-marketing.docs.json-section>
          </x-marketing.docs.json-section>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/organizations/{id}/adminland/locations/{locationId} -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="update-a-location" title="Update a location" />
        <p class="mb-2">This endpoint updates an existing location.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          location.manage
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
          <x-marketing.docs.attribute required name="locationId" type="integer" description="The ID of the location to update." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="branch_id" type="integer" description="The ID of the branch this location belongs to." />
          <x-marketing.docs.attribute required name="name" type="string" description="The name of the location. Maximum 100 characters." />
          <x-marketing.docs.attribute name="parent_id" type="integer" description="The ID of a parent location for nested locations." />
          <x-marketing.docs.attribute name="code" type="string" description="A short code identifying the location. Maximum 50 characters." />
          <x-marketing.docs.attribute name="description" type="string" description="A description of the location. Maximum 255 characters." />
          <x-marketing.docs.attribute name="is_active" type="boolean" description="Whether the location is active." />
          <x-marketing.docs.attribute name="is_public" type="boolean" description="Whether the location is visible to patrons." />
          <x-marketing.docs.attribute name="supports_pickups" type="boolean" description="Whether items can be picked up at this location." />
          <x-marketing.docs.attribute name="supports_returns" type="boolean" description="Whether items can be returned at this location." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource. Always 'location'." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the location." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the location." />
          <x-marketing.docs.attribute name="attributes.organization_id" type="integer" description="The ID of the organization." />
          <x-marketing.docs.attribute name="attributes.branch_id" type="integer" description="The updated branch ID." />
          <x-marketing.docs.attribute name="attributes.parent_id" type="integer" description="The updated parent location ID. Can be null." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The updated name." />
          <x-marketing.docs.attribute name="attributes.code" type="string" description="The updated code. Can be null." />
          <x-marketing.docs.attribute name="attributes.description" type="string" description="The updated description. Can be null." />
          <x-marketing.docs.attribute name="attributes.is_active" type="boolean" description="Updated active status." />
          <x-marketing.docs.attribute name="attributes.is_public" type="boolean" description="Updated public visibility." />
          <x-marketing.docs.attribute name="attributes.supports_pickups" type="boolean" description="Updated pickups support flag." />
          <x-marketing.docs.attribute name="attributes.supports_returns" type="boolean" description="Updated returns support flag." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the location." />
          <x-marketing.docs.attribute name="links.self" type="string" description="The URL of the location." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/locations/{locationId}" verb="PUT">
          <x-marketing.docs.json-section level="1" name="data">
            <x-marketing.docs.json-line level="2" key="type" value="location" type="string" comma />
            <x-marketing.docs.json-line level="2" key="id" value="1" type="string" comma />
            <x-marketing.docs.json-section level="2" name="attributes" comma>
              <x-marketing.docs.json-line level="3" key="organization_id" value="1" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="branch_id" value="1" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="parent_id" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="name" value="Updated Stacks" type="string" comma />
              <x-marketing.docs.json-line level="3" key="code" value="US" type="string" comma />
              <x-marketing.docs.json-line level="3" key="description" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_active" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_public" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="supports_pickups" value="false" type="string" comma />
              <x-marketing.docs.json-line level="3" key="supports_returns" value="false" type="string" comma />
              <x-marketing.docs.json-line level="3" key="created_at" value="1771898698" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="updated_at" value="1771898699" type="integer" />
            </x-marketing.docs.json-section>
            <x-marketing.docs.json-section level="2" name="links">
              <x-marketing.docs.json-line level="3" key="self" value="http://libraryOS.test/api/organizations/1/adminland/locations/1" type="string" />
            </x-marketing.docs.json-section>
          </x-marketing.docs.json-section>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- DELETE /api/organizations/{id}/adminland/locations/{locationId} -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="delete-a-location" title="Delete a location" />
        <p class="mb-2">This endpoint permanently deletes a location from the organization.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          location.manage
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
          <x-marketing.docs.attribute required name="locationId" type="integer" description="The ID of the location to delete." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters no-parameters></x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes no-parameters></x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/locations/{locationId}" verb="DELETE">
          <div>No response body</div>
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
