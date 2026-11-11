<form id="viewFloorForm">
    <div class="row">
        <div class="mb-3 col-12 col-md-4 col-lg-3">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" min="0" id="view_name"
                    class="form-control w-full px-3 py-2 border rounded" placeholder="Enter name..." readonly>
            </div>
        </div>
        <div class="mb-3 col-12 col-md-4 col-lg-3">
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" class="form-control w-full px-3 py-2 border rounded" placeholder="Enter Description..."
                    id="view_description" readonly></textarea>
            </div>
        </div>
        <div class="mb-4 w-full md:w-1/2 lg:w-1/3">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <div class="flex items-center space-x-3">
                <label class="inline-flex items-center">
                    <input type="radio" id="view_status_active" name="view_status" value="1" disabled>
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="radio" id="view_status_inactive" name="view_status" value="0" disabled>
                    <span class="ml-2 text-sm text-gray-700">Inactive</span>
                </label>
            </div>
        </div>

        <div class="mb-3 text-right">
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded hover:bg-primary-dark">Save</button>
            <button type="button" class="ml-2 bg-gray-400 text-white px-4 py-2 rounded"
                id="closeViewModal">Close</button>
        </div>
    </div>
</form>
