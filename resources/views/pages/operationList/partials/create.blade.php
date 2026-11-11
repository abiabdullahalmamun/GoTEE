<form method="POST" id="operationListForm" action="{{ route('operationList.store') }}">
    @csrf
  @method('POST')
    <input type="hidden" name="OrderCode" id="modalOrderCode">

    <div class="flex justify-between items-center mb-3">
        <div class="font-semibold">
            Order: <span id="modalOrderName"></span>
        </div>

        <button type="button" id="addRow"
                class="bg-purple-700 text-white px-4 py-2 rounded">
            + Add Row
        </button>
    </div>

    <div class="overflow-x-auto border rounded">
        <table id="operationTable"
               class="min-w-[1100px] w-full text-sm border-collapse">

            <thead class="bg-purple-100">
                <tr>
                    <th class="p-2 border text-center"></th>
                    <th class="p-2 border">Seq</th>
                    <th class="p-2 border">Operation Name</th>
                    <th class="p-2 border">Operation Code</th>
                    <th class="p-2 border">Machine Type</th>
                    <th class="p-2 border">SMV</th>
                    <th class="p-2 border">Target</th>
                    <th class="p-2 border">ManPower</th>
                    <th class="p-2 border">Category</th>
                    <th class="p-2 border">Action</th>
                </tr>
            </thead>

            <tbody id="operationTableBody"></tbody>
        </table>
    </div>

    <div class="flex justify-end gap-2 mt-4">
        <button type="submit" id="saveOperationList"
                class="bg-green-600 text-white px-5 py-2 rounded">
            Save
        </button>

        <button type="button" id="closeModal"
                class="bg-gray-500 text-white px-5 py-2 rounded">
            Cancel
        </button>
    </div>
</form>

<script>
    window.operationMasterData = {
        operations: @json($operationCodes),
        machineTypes: @json($machineTypes),
        processGroups: @json($processGroups)
    };
</script>

 
