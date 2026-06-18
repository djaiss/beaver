<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.show', ['version' => request()->route('version'), 'path' => 'api'])],
  ['label' => 'Organizations', 'route' => route('marketing.docs.show', ['version' => request()->route('version'), 'path' => 'api/organizations'])],
  ['label' => 'Patron Types'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Patron Types" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'list-patron-types',
        'title' => 'List all patron types',
      ],
      [
        'id' => 'get-a-patron-type',
        'title' => 'Get a specific patron type',
      ],
      [
        'id' => 'create-a-patron-type',
        'title' => 'Create a patron type',
      ],
      [
        'id' => 'update-a-patron-type',
        'title' => 'Update a patron type',
      ],
      [
        'id' => 'delete-a-patron-type',
        'title' => 'Delete a patron type',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <p class="mb-2">Patron types define the different categories of library members and control their borrowing privileges (e.g. "Adult", "Child", "Senior").</p>
        <p>
          All patron type endpoints require you to be a member of the organization. Create, update, and delete operations additionally require the
          <strong>patron_type.manage</strong>
          permission.
        </p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#list-patron-types">
              <span class="text-blue-700">GET</span>
              /api/organizations/{id}/adminland/patron-types
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#get-a-patron-type">
              <span class="text-blue-700">GET</span>
              /api/organizations/{id}/adminland/patron-types/{patronTypeId}
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#create-a-patron-type">
              <span class="text-green-700">POST</span>
              /api/organizations/{id}/adminland/patron-types
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#update-a-patron-type">
              <span class="text-yellow-700">PUT</span>
              /api/organizations/{id}/adminland/patron-types/{patronTypeId}
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#delete-a-patron-type">
              <span class="text-red-700">DELETE</span>
              /api/organizations/{id}/adminland/patron-types/{patronTypeId}
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- GET /api/organizations/{id}/adminland/patron-types -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="list-patron-types" title="List all patron types" />
        <p class="mb-2">This endpoint returns all patron types belonging to the given organization, ordered alphabetically by name.</p>
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
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource. Always 'patron_type'." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the patron type." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the patron type." />
          <x-marketing.docs.attribute name="attributes.key" type="string" description="The unique key identifying this patron type (e.g. 'adult')." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The display name of the patron type." />
          <x-marketing.docs.attribute name="attributes.description" type="string" description="A short description of the patron type. Can be null." />
          <x-marketing.docs.attribute name="attributes.is_active" type="boolean" description="Whether patrons can be assigned this type." />
          <x-marketing.docs.attribute name="attributes.membership_duration_days" type="integer" description="The membership duration in days. Can be null." />
          <x-marketing.docs.attribute name="attributes.max_loans" type="integer" description="The maximum number of simultaneous loans. Can be null." />
          <x-marketing.docs.attribute name="attributes.keep_loan_history" type="boolean" description="Whether loan history is retained after items are returned." />
          <x-marketing.docs.attribute name="attributes.can_receive_notifications" type="boolean" description="Whether patrons of this type can receive email and system notifications." />
          <x-marketing.docs.attribute name="attributes.minimum_age" type="integer" description="The minimum age for this patron type. Can be null." />
          <x-marketing.docs.attribute name="attributes.maximum_age" type="integer" description="The maximum age for this patron type. Can be null." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the patron type." />
          <x-marketing.docs.attribute name="links.self" type="string" description="The URL of the patron type." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/patron-types" verb="GET">
          <x-marketing.docs.json-section level="1" name="data">
            <x-marketing.docs.json-line level="2" key="type" value="patron_type" type="string" comma />
            <x-marketing.docs.json-line level="2" key="id" value="1" type="string" comma />
            <x-marketing.docs.json-section level="2" name="attributes" comma>
              <x-marketing.docs.json-line level="3" key="key" value="adult" type="string" comma />
              <x-marketing.docs.json-line level="3" key="name" value="Adult" type="string" comma />
              <x-marketing.docs.json-line level="3" key="description" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_active" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="membership_duration_days" value="365" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="max_loans" value="5" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="keep_loan_history" value="false" type="string" comma />
              <x-marketing.docs.json-line level="3" key="can_receive_notifications" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="minimum_age" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="maximum_age" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="created_at" value="1771898698" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="updated_at" value="1771898698" type="integer" />
            </x-marketing.docs.json-section>
            <x-marketing.docs.json-section level="2" name="links">
              <x-marketing.docs.json-line level="3" key="self" value="http://libraryOS.test/api/organizations/1/adminland/patron-types/1" type="string" />
            </x-marketing.docs.json-section>
          </x-marketing.docs.json-section>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- GET /api/organizations/{id}/adminland/patron-types/{patronTypeId} -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="get-a-patron-type" title="Get a specific patron type" />
        <p class="mb-2">This endpoint returns a specific patron type belonging to the given organization.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          any member of the organization.
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
          <x-marketing.docs.attribute required name="patronTypeId" type="integer" description="The ID of the patron type." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters no-parameters></x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource. Always 'patron_type'." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the patron type." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the patron type." />
          <x-marketing.docs.attribute name="attributes.key" type="string" description="The unique key identifying this patron type." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The display name of the patron type." />
          <x-marketing.docs.attribute name="attributes.description" type="string" description="A short description. Can be null." />
          <x-marketing.docs.attribute name="attributes.is_active" type="boolean" description="Whether patrons can be assigned this type." />
          <x-marketing.docs.attribute name="attributes.membership_duration_days" type="integer" description="The membership duration in days. Can be null." />
          <x-marketing.docs.attribute name="attributes.max_loans" type="integer" description="The maximum number of simultaneous loans. Can be null." />
          <x-marketing.docs.attribute name="attributes.keep_loan_history" type="boolean" description="Whether loan history is retained after items are returned." />
          <x-marketing.docs.attribute name="attributes.can_receive_notifications" type="boolean" description="Whether patrons of this type can receive notifications." />
          <x-marketing.docs.attribute name="attributes.minimum_age" type="integer" description="The minimum age for this patron type. Can be null." />
          <x-marketing.docs.attribute name="attributes.maximum_age" type="integer" description="The maximum age for this patron type. Can be null." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the patron type." />
          <x-marketing.docs.attribute name="links.self" type="string" description="The URL of the patron type." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/patron-types/{patronTypeId}" verb="GET">
          <x-marketing.docs.json-section level="1" name="data">
            <x-marketing.docs.json-line level="2" key="type" value="patron_type" type="string" comma />
            <x-marketing.docs.json-line level="2" key="id" value="1" type="string" comma />
            <x-marketing.docs.json-section level="2" name="attributes" comma>
              <x-marketing.docs.json-line level="3" key="key" value="adult" type="string" comma />
              <x-marketing.docs.json-line level="3" key="name" value="Adult" type="string" comma />
              <x-marketing.docs.json-line level="3" key="description" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_active" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="membership_duration_days" value="365" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="max_loans" value="5" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="keep_loan_history" value="false" type="string" comma />
              <x-marketing.docs.json-line level="3" key="can_receive_notifications" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="minimum_age" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="maximum_age" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="created_at" value="1771898698" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="updated_at" value="1771898698" type="integer" />
            </x-marketing.docs.json-section>
            <x-marketing.docs.json-section level="2" name="links">
              <x-marketing.docs.json-line level="3" key="self" value="http://libraryOS.test/api/organizations/1/adminland/patron-types/1" type="string" />
            </x-marketing.docs.json-section>
          </x-marketing.docs.json-section>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- POST /api/organizations/{id}/adminland/patron-types -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="create-a-patron-type" title="Create a patron type" />
        <p class="mb-2">This endpoint creates a new patron type for the given organization.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          patron_type.manage
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="key" type="string" description="A unique key identifying this patron type (e.g. 'adult'). Maximum 100 characters." />
          <x-marketing.docs.attribute required name="name" type="string" description="The display name of the patron type. Maximum 100 characters." />
          <x-marketing.docs.attribute name="description" type="string" description="A short description. Maximum 255 characters." />
          <x-marketing.docs.attribute name="is_active" type="boolean" description="Whether patrons can be assigned this type. Defaults to true." />
          <x-marketing.docs.attribute name="membership_duration_days" type="integer" description="The membership duration in days. Must be at least 1." />
          <x-marketing.docs.attribute name="max_loans" type="integer" description="The maximum number of simultaneous loans. Must be at least 1." />
          <x-marketing.docs.attribute name="keep_loan_history" type="boolean" description="Whether loan history is retained after items are returned. Defaults to false." />
          <x-marketing.docs.attribute name="can_receive_notifications" type="boolean" description="Whether patrons of this type can receive notifications. Defaults to true." />
          <x-marketing.docs.attribute name="minimum_age" type="integer" description="The minimum age for this patron type. Must be at least 0." />
          <x-marketing.docs.attribute name="maximum_age" type="integer" description="The maximum age for this patron type. Must be at least 0." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource. Always 'patron_type'." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the newly created patron type." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the patron type." />
          <x-marketing.docs.attribute name="attributes.key" type="string" description="The unique key." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The display name." />
          <x-marketing.docs.attribute name="attributes.description" type="string" description="The description. Can be null." />
          <x-marketing.docs.attribute name="attributes.is_active" type="boolean" description="Whether patrons can be assigned this type." />
          <x-marketing.docs.attribute name="attributes.membership_duration_days" type="integer" description="The membership duration in days. Can be null." />
          <x-marketing.docs.attribute name="attributes.max_loans" type="integer" description="The maximum simultaneous loans. Can be null." />
          <x-marketing.docs.attribute name="attributes.keep_loan_history" type="boolean" description="Whether loan history is retained." />
          <x-marketing.docs.attribute name="attributes.can_receive_notifications" type="boolean" description="Whether patrons can receive notifications." />
          <x-marketing.docs.attribute name="attributes.minimum_age" type="integer" description="The minimum age. Can be null." />
          <x-marketing.docs.attribute name="attributes.maximum_age" type="integer" description="The maximum age. Can be null." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the patron type." />
          <x-marketing.docs.attribute name="links.self" type="string" description="The URL of the patron type." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/patron-types" verb="POST">
          <x-marketing.docs.json-section level="1" name="data">
            <x-marketing.docs.json-line level="2" key="type" value="patron_type" type="string" comma />
            <x-marketing.docs.json-line level="2" key="id" value="1" type="string" comma />
            <x-marketing.docs.json-section level="2" name="attributes" comma>
              <x-marketing.docs.json-line level="3" key="key" value="adult" type="string" comma />
              <x-marketing.docs.json-line level="3" key="name" value="Adult" type="string" comma />
              <x-marketing.docs.json-line level="3" key="description" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_active" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="membership_duration_days" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="max_loans" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="keep_loan_history" value="false" type="string" comma />
              <x-marketing.docs.json-line level="3" key="can_receive_notifications" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="minimum_age" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="maximum_age" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="created_at" value="1771898698" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="updated_at" value="1771898698" type="integer" />
            </x-marketing.docs.json-section>
            <x-marketing.docs.json-section level="2" name="links">
              <x-marketing.docs.json-line level="3" key="self" value="http://libraryOS.test/api/organizations/1/adminland/patron-types/1" type="string" />
            </x-marketing.docs.json-section>
          </x-marketing.docs.json-section>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/organizations/{id}/adminland/patron-types/{patronTypeId} -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="update-a-patron-type" title="Update a patron type" />
        <p class="mb-2">This endpoint updates an existing patron type.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          patron_type.manage
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
          <x-marketing.docs.attribute required name="patronTypeId" type="integer" description="The ID of the patron type to update." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="key" type="string" description="A unique key identifying this patron type. Maximum 100 characters." />
          <x-marketing.docs.attribute required name="name" type="string" description="The display name of the patron type. Maximum 100 characters." />
          <x-marketing.docs.attribute name="description" type="string" description="A short description. Maximum 255 characters." />
          <x-marketing.docs.attribute name="is_active" type="boolean" description="Whether patrons can be assigned this type." />
          <x-marketing.docs.attribute name="membership_duration_days" type="integer" description="The membership duration in days. Must be at least 1." />
          <x-marketing.docs.attribute name="max_loans" type="integer" description="The maximum number of simultaneous loans. Must be at least 1." />
          <x-marketing.docs.attribute name="keep_loan_history" type="boolean" description="Whether loan history is retained after items are returned." />
          <x-marketing.docs.attribute name="can_receive_notifications" type="boolean" description="Whether patrons of this type can receive notifications." />
          <x-marketing.docs.attribute name="minimum_age" type="integer" description="The minimum age for this patron type. Must be at least 0." />
          <x-marketing.docs.attribute name="maximum_age" type="integer" description="The maximum age for this patron type. Must be at least 0." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource. Always 'patron_type'." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the patron type." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the patron type." />
          <x-marketing.docs.attribute name="attributes.key" type="string" description="The updated key." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The updated name." />
          <x-marketing.docs.attribute name="attributes.description" type="string" description="The updated description. Can be null." />
          <x-marketing.docs.attribute name="attributes.is_active" type="boolean" description="Updated active status." />
          <x-marketing.docs.attribute name="attributes.membership_duration_days" type="integer" description="Updated membership duration. Can be null." />
          <x-marketing.docs.attribute name="attributes.max_loans" type="integer" description="Updated maximum loans. Can be null." />
          <x-marketing.docs.attribute name="attributes.keep_loan_history" type="boolean" description="Updated loan history retention flag." />
          <x-marketing.docs.attribute name="attributes.can_receive_notifications" type="boolean" description="Updated notification flag." />
          <x-marketing.docs.attribute name="attributes.minimum_age" type="integer" description="Updated minimum age. Can be null." />
          <x-marketing.docs.attribute name="attributes.maximum_age" type="integer" description="Updated maximum age. Can be null." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the patron type." />
          <x-marketing.docs.attribute name="links.self" type="string" description="The URL of the patron type." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/patron-types/{patronTypeId}" verb="PUT">
          <x-marketing.docs.json-section level="1" name="data">
            <x-marketing.docs.json-line level="2" key="type" value="patron_type" type="string" comma />
            <x-marketing.docs.json-line level="2" key="id" value="1" type="string" comma />
            <x-marketing.docs.json-section level="2" name="attributes" comma>
              <x-marketing.docs.json-line level="3" key="key" value="adult-updated" type="string" comma />
              <x-marketing.docs.json-line level="3" key="name" value="Adult Updated" type="string" comma />
              <x-marketing.docs.json-line level="3" key="description" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="is_active" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="membership_duration_days" value="365" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="max_loans" value="5" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="keep_loan_history" value="false" type="string" comma />
              <x-marketing.docs.json-line level="3" key="can_receive_notifications" value="true" type="string" comma />
              <x-marketing.docs.json-line level="3" key="minimum_age" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="maximum_age" value="null" type="string" comma />
              <x-marketing.docs.json-line level="3" key="created_at" value="1771898698" type="integer" comma />
              <x-marketing.docs.json-line level="3" key="updated_at" value="1771898699" type="integer" />
            </x-marketing.docs.json-section>
            <x-marketing.docs.json-section level="2" name="links">
              <x-marketing.docs.json-line level="3" key="self" value="http://libraryOS.test/api/organizations/1/adminland/patron-types/1" type="string" />
            </x-marketing.docs.json-section>
          </x-marketing.docs.json-section>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- DELETE /api/organizations/{id}/adminland/patron-types/{patronTypeId} -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="delete-a-patron-type" title="Delete a patron type" />
        <p class="mb-2">This endpoint permanently deletes a patron type from the organization.</p>
        <p class="mb-10">
          <strong>Required permission:</strong>
          patron_type.manage
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the organization." />
          <x-marketing.docs.attribute required name="patronTypeId" type="integer" description="The ID of the patron type to delete." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters no-parameters></x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes no-parameters></x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/organizations/{id}/adminland/patron-types/{patronTypeId}" verb="DELETE">
          <div>No response body</div>
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
