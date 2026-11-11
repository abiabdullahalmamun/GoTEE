import{$ as e}from"./jquery-D9RDqKhM.js";import{D as y}from"./dataTables-BQ--1YPe.js";import"./_commonjsHelpers-D6-XlEtG.js";document.addEventListener("DOMContentLoaded",function(){e(document).on("click",".order-select-btn",function(){const t=e(this).data("url");e("#details").html(`
            <div class="text-center py-10 text-gray-500">
                Loading OperationList...
            </div>
        `),e.ajax({url:t,type:"GET",success:function(o){e("#details").html(o)},error:function(o){console.error(o.responseText),e("#details").html(`
                    <div class="text-center py-10 text-red-600">
                        Unable to load OperationList.
                    </div>
                `)}})});const r=document.getElementById("ShopModal");function f(){r&&(r.classList.remove("hidden"),r.classList.add("flex"))}function d(){r&&(r.classList.add("hidden"),r.classList.remove("flex"))}e(document).on("input change",".operation-code-input",function(){const t=e(this).val().trim(),o=e(this).closest("tr"),a=o.find(".operation-name-select"),n=a.find("option").filter(function(){return String(e(this).data("code"))===t}).first();a.val(n.length?n.val():""),o.find(".last-operation-radio").val(t)}),e(document).on("change",".operation-name-select",function(){const o=e(this).find(":selected").data("code")||"";e(this).closest("tr").find(".operation-code-input").val(o)}),e(document).on("change",".last-operation-radio",function(){e("#operationTableBody .operation-row").removeClass("bg-yellow-200"),e(this).closest("tr").addClass("bg-yellow-200")}),e(document).on("input",'input[name="SMV[]"]',function(){const t=parseFloat(e(this).val()),a=e(this).closest("tr").find('input[name="Target[]"]');!isNaN(t)&&t>0?a.val(Math.round(60/t)):a.val("")});function l(t={},o=[],a=[],n=[]){var m;const i=o.map(s=>`
        <option value="${s.id}"
            ${String(s.id)===String(t.grpCode)?"selected":""}>
            ${s.groupName}
        </option>
    `).join(""),p=a.map(s=>`
        <option value="${s.id}"
            ${String(s.id)===String(t.MC_Type??"").trim()?"selected":""}>
            ${s.typeName}
        </option>
    `).join("");t.OprNo;const c=t.OperationCode??"";t.OperationName??((m=t.operation)==null||m.OperationName);const v=n.map(s=>`
        <option value="${s.OperationName}"
            data-code="${s.OprNo}"
            ${String(s.OprNo)===String(c)?"selected":""}>
            ${s.OperationName}
        </option>
    `).join(""),h=`
    <tr class="operation-row border-b ${t.IsLastOperation==="Y"?"bg-yellow-200":""}">    

            <td class="px-2 py-2 text-center w-[50px]">
                <input type="radio"
                    name="IsLastOperation"
                    value="${t.OperationCode??""}"
                    class="last-operation-radio"
                    ${t.IsLastOperation==="Y"?"checked":""}>
            </td>

          <td class="px-1 py-2 w-[100px]">
                <input type="number" step="0.01" name="OperationSeqNo[]"
                    value="${t.OperationSeqNo??""}"
                    class="form-input w-full">
            </td>

            <td class="px-1 py-2 w-[90px]">
                <input type="text" name="OperationCode[]"
                    value="${c}"
                    class="form-input w-full operation-code-input">
            </td>

            <td class="px-2 py-2 min-w-[250px]">
                <select name="OperationName[]"
                    class="form-select w-full operation-name-select">
                    <option value=""></option>
                    ${v}
                </select>
            </td>


            <td class="px-1 py-2 w-[130px]">
                <select name="MC_Type[]" class="form-select w-full">
                    <option value=""></option>
                    ${p}
                </select>
            </td>

            <td class="px-1 py-2 w-[100px]">
                <input type="number" step="0.01" name="SMV[]"
                    value="${t.SMV??""}"
                    class="form-input w-full">
            </td>

            <td class="px-1 py-2 w-[100px]">
                <input type="number" step="1" name="Target[]"
                    value="${t.Target??""}"
                    class="form-input w-full">
            </td>

            <td class="px-1 py-2 w-[65px]">
                <input type="number" name="ManPower[]"
                    value="${t.ManPower??""}"
                    class="form-input w-full">
            </td>

            <td class="px-1 py-2 w-[85px]">
                <select name="grpCode[]" class="form-select w-full">
                    <option value=""></option>
                    ${i}
                </select>
            </td>

            <td class="px-1 py-2 w-[45px] text-center">
                <button type="button"
                    class="remove-operation text-red-600">
                    <i class="fas fa-trash"></i>
                </button>
            </td>

        </tr>
    `;e("#operationTableBody").append(h)}e(document).on("click",".remove-operation",function(){e(this).closest("tr").remove()}),e(document).on("click","#addOperations",function(){const t=e(this).attr("data-order-code"),o=e(this).attr("data-order-name");let a=[];try{a=JSON.parse(e(this).attr("data-operations")||"[]")}catch(i){console.error("Invalid operations JSON:",i)}const n=window.operationMasterData;e("#modalOrderCode").val(t),e("#modalOrderName").text(o),e("#operationTableBody").empty(),a.length>0?a.forEach(function(i){l(i,n.processGroups||[],n.machineTypes||[],n.operations||[])}):l({},n.processGroups||[],n.machineTypes||[],n.operations||[]),f()}),e(document).on("click","#closeModal",d),r==null||r.addEventListener("click",function(t){t.target===r&&d()}),e(document).on("click","#addRow",function(){const t=window.operationMasterData;l({},t.processGroups||[],t.machineTypes||[],t.operations||[])}),e(document).on("click","#RemoveOperations",function(t){t.preventDefault();const o=e(this),a=o.data("order-code"),n=o.data("url");console.log("DELETE URL:",n),confirm(`Are you sure you want to remove all operations for Order ${a}?`)&&(o.prop("disabled",!0).text("Removing..."),e.ajax({url:n,method:"DELETE",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content")},success:function(i){alert(i.message),location.reload()},error:function(i){var c;console.error(i.responseText);const p=((c=i.responseJSON)==null?void 0:c.message)||"Failed to remove operation list.";alert(p),o.prop("disabled",!1).text("Remove List")}}))});const u=document.getElementById("deviceTable");u&&new y(u,{responsive:!0,pageLength:500,language:{search:"Search:",lengthMenu:"Show _MENU_ entries per page",zeroRecords:"No matching data found",info:"Showing _START_ to _END_ of _TOTAL_ data",infoEmpty:"No data available",infoFiltered:"(filtered from _MAX_ total data)"}})});
