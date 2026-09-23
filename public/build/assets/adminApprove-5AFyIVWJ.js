import{D as x}from"./dataTables-CHR-Id-R.js";import"./_commonjsHelpers-D6-XlEtG.js";document.addEventListener("DOMContentLoaded",()=>{const a=document.getElementById("latest-records"),b=document.querySelector('meta[name="csrf-token"]').getAttribute("content"),o=document.getElementById("actId"),l=document.getElementById("from_date");o&&o.addEventListener("change",function(){f(this.value)});function f(n=""){const s=o.value,r=l?l.value.trim():"";fetch("/AdminApproval",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content"),"X-Requested-With":"XMLHttpRequest"},body:JSON.stringify({act:s,fromDate:r})}).then(async t=>{const e=t.headers.get("content-type");if(!e||!e.includes("application/json"))throw new Error("Permission denied");return t.json()}).then(t=>{t.success?(i(t.latest),document.getElementById("count").textContent=t.count||"0",document.getElementById("smsg").textContent=t.smsg):document.getElementById("smsg").textContent=t.smsg||"Failed to save record."}).catch(t=>{document.getElementById("smsg").textContent=t.message})}function i(n,s){if(!n.length){a.innerHTML='<p class="text-gray-500">No recent records.</p>';return}let r=`
        <table id="latestRecordsTable" class="table-auto w-full border border-gray-300 text-xs">
            <thead class="bg-gray-200"><tr>
                <th class="border px-2 py-1">S.No</th>`;r+=`
            <th class="border px-2 py-1">Date</th>
            <th class="border px-2 py-1">Center</th>
            <th class="border px-2 py-1">Webfile</th>
            <th class="border px-2 py-1">VisaType</th>
            <th class="border px-2 py-1">Remarks</th>
            
            <th class="border px-2 py-1">Type</th>
            <th class="border px-2 py-1">Status</th>
            <th class="border px-2 py-1">CreatedBy</th>
            <th class="border px-2 py-1">CreatedAt</th>
            <th class="border px-2 py-1">ApprovedBy</th>
            <th class="border px-2 py-1">ApprovedAt</th>
            <th class="border px-2 py-1">Action</th>
        `,r+="</tr></thead><tbody>",n.forEach((e,d)=>{var u,m,y,g;r+=`<tr><td class="border px-2 py-1 text-center">${d+1}</td>`;const c={0:"Pending",1:"Approved"},p={1:"Foreign Passport",2:"WAIVE",3:"Others"};r+=`
                <td class="border px-2 py-1">${e.Date||""}</td>
                <td class="border px-2 py-1">${((u=e.center)==null?void 0:u.center_name)||""}</td>
               
                <td class="border px-2 py-1">${e.WebFile_no||""}</td>
               
                <td class="border px-2 py-1">${((m=e.visa)==null?void 0:m.visa_type)||""}</td>
                 <td class="border px-2 py-1">${e.remarks||""}</td>
                <td class="border px-2 py-1">${p[e.svcId]||""}</td>
                <td class="border px-2 py-1 text-center ${e.active===1?"text-green-600":"text-red-600"}">
                  ${c[e.active]||""}
                </td>
                <td class="border px-2 py-1">${((y=e.user)==null?void 0:y.name)||""}</td>
                 <td class="border px-2 py-1 text-center">${h(e.created_at)}</td>
                <td class="border px-2 py-1">
               ${((g=e.user_app)==null?void 0:g.name)??"—"}
                  </td>
                <td class="border px-2 py-1 text-center"> ${e.approvedAt?h(e.approvedAt):""}</td>
                 <td class="px-2 py-1 border text-center">
                  ${e.active===0?`<button type="button" 
                                 class="approve-btn bg-green-500 hover:bg-green-700 text-white px-2 py-1 rounded"
                                 data-id="${e.id||""}">
                           <i class="fas fa-check"></i>
                         </button>`:""}
                </td>

            `,r+="</tr>"}),r+="</tbody></table>",a.innerHTML=r;const t=document.getElementById("latestRecordsTable");t&&new x(t,{responsive:!0,pageLength:100,language:{search:"Search:",lengthMenu:"Show _MENU_ entries per page",zeroRecords:"No matching records found",info:"Showing _START_ to _END_ of _TOTAL_ records",infoEmpty:"No records available",infoFiltered:"(filtered from _MAX_ total records)"}})}function h(n){const s=new Date(n);if(isNaN(s))return"";const r=s.getFullYear(),t=String(s.getMonth()+1).padStart(2,"0"),e=String(s.getDate()).padStart(2,"0"),d=String(s.getHours()).padStart(2,"0"),c=String(s.getMinutes()).padStart(2,"0"),p=String(s.getSeconds()).padStart(2,"0");return`${r}-${t}-${e} ${d}:${c}:${p}`}a.addEventListener("click",function(n){if(n.target.closest(".approve-btn")){n.preventDefault();const r=n.target.closest(".approve-btn").dataset.id;if(o.value,!confirm("Are you sure?"))return;fetch(`/AdminApproval/${r}`,{method:"DELETE",headers:{"X-CSRF-TOKEN":b,Accept:"application/json"}}).then(async t=>{const e=t.headers.get("content-type");if(!e||!e.includes("application/json"))throw new Error("Permission denied");return t.json()}).then(t=>{if(!t.success)throw new Error(t.message||"Delete failed");o&&o.value,i(t.latest),document.getElementById("smsg").textContent="Record Updated",alert(t.message||"Record Updated")}).catch(t=>{alert(t.message)})}})});
