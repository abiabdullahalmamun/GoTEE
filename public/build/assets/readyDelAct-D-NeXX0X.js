import{D as m}from"./dataTables-CHR-Id-R.js";import"./_commonjsHelpers-D6-XlEtG.js";document.addEventListener("DOMContentLoaded",()=>{const c=document.getElementById("latest-records"),x=document.querySelector('meta[name="csrf-token"]').getAttribute("content"),n=document.getElementById("actId"),i=document.getElementById("from_date");n&&n.addEventListener("change",function(){f(this.value)});function f(d=""){const s=n.value,r=i?i.value.trim():"";fetch("/readyDelAct",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content"),"X-Requested-With":"XMLHttpRequest"},body:JSON.stringify({act:s,fromDate:r})}).then(async e=>{const t=e.headers.get("content-type");if(!t||!t.includes("application/json"))throw new Error("Permission denied");return e.json()}).then(e=>{e.success?(b(e.latest,s),document.getElementById("count").textContent=e.count||"0",document.getElementById("smsg").textContent=e.smsg):document.getElementById("smsg").textContent=e.smsg||"Failed to save record."}).catch(e=>{document.getElementById("smsg").textContent=e.message})}function b(d,s){if(!d.length){c.innerHTML='<p class="text-gray-500">No recent records.</p>';return}s=Number(s);let r=`
        <table id="latestRecordsTable" class="table-auto w-full border border-gray-300 text-xs">
            <thead class="bg-gray-200"><tr>
                <th class="border px-2 py-1">S.No</th>`;s===10?r+=`
            <th class="border px-2 py-1">Webfile</th>
            <th class="border px-2 py-1">Passport</th>
            <th class="border px-2 py-1">Name</th>
            <th class="border px-2 py-1">Contact</th>
            <th class="border px-2 py-1">Day SL</th>
            <th class="border px-2 py-1">Fee</th>
            <th class="border px-2 py-1">Time</th>
            <th class="border px-2 py-1">Action</th>
        `:s===20?r+=`
            <th class="border px-2 py-1">Webfile</th>
            <th class="border px-2 py-1">Passport</th>
            <th class="border px-2 py-1">Nationality</th>
            <th class="border px-2 py-1">Applicant</th>
            <th class="border px-2 py-1">Contact</th>
            <th class="border px-2 py-1">Visa Type</th>
            <th class="border px-2 py-1">Sticker</th>
            <th class="border px-2 py-1">Sticker No</th>
            <th class="border px-2 py-1">Corr.Fee</th>
            <th class="border px-2 py-1">Receipt</th>
            <th class="border px-2 py-1">TotalFee</th>
            <th class="border px-2 py-1">Time</th>
            <th class="border px-2 py-1">Action</th>
        `:r+=`
            <th class="border px-2 py-1">Webfile</th>
            <th class="border px-2 py-1">Passport</th>
            <th class="border px-2 py-1">Applicant</th>
            <th class="border px-2 py-1">Contact</th>
            <th class="border px-2 py-1">Visa Type</th>
            <th class="border px-2 py-1">Sticker</th>
            <th class="border px-2 py-1">Sticker No</th>
            <th class="border px-2 py-1">Corr.Fee</th>
            <th class="border px-2 py-1">Remarks</th>
            <th class="border px-2 py-1">Time</th>
            <th class="border px-2 py-1">Action</th>
        `,r+="</tr></thead><tbody>",d.forEach((t,l)=>{var a,y,h,u;const o=t.webref||{};r+=`<tr><td class="border px-2 py-1 text-center">${l+1}</td>`,s===10?r+=`
                <td class="border px-2 py-1">${t.webfile||""}</td>
                <td class="border px-2 py-1">${t.passport||""}</td>
                <td class="border px-2 py-1">${t.Name||""}</td>
                <td class="border px-2 py-1">${t.contact||""}</td>
                <td class="border px-2 py-1 text-center">${t.day_sl||""}</td>
                <td class="border px-2 py-1 text-center">${t.fee||0}</td>
                <td class="border px-2 py-1 text-center">${p(t.created_at)}</td>
                <td class="px-2 py-1 border text-center">
                    ${`<button type="button" 
                            class="print-btn2 bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 rounded"
                            data-id="${t.id||""}">
                            <i class="fas fa-print"></i>
                      </button>`}
                </td>
            `:s===20?r+=`
                <td class="border px-2 py-1">${o.Webfile||""}</td>
                <td class="border px-2 py-1">${o.passport||""}</td>
                <td class="border px-2 py-1">${t.nationality||""}</td>
                <td class="border px-2 py-1">${o.ApplicantName||""}</td>
                <td class="border px-2 py-1">${o.contact||""}</td>
                <td class="border px-2 py-1">${((a=o.visa)==null?void 0:a.visa_type)||""}</td>
                <td class="border px-2 py-1">${((y=o.sticker)==null?void 0:y.sticker)||""}</td>
                <td class="border px-2 py-1">${o.stickerNo||""}</td>
                <td class="border px-2 py-1 text-center">${o.corrFee||0}</td>
                 <td class="border px-2 py-1">${t.ReceiptNo||""}</td>
                <td class="border px-2 py-1">${t.total_amount||""}</td>
                <td class="border px-2 py-1 text-center">${p(t.created_at)}</td>
                 <td class="px-2 py-1 border text-center">
                    <button type="button"
                        class="print-btn3 bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 rounded"
                        data-id="${t.web_ref||""}">
                        <i class="fas fa-print"></i>
                    </button>
                </td>
            `:r+=`
                <td class="border px-2 py-1">${o.Webfile||""}</td>
                <td class="border px-2 py-1">${o.passport||""}</td>
                <td class="border px-2 py-1">${o.ApplicantName||""}</td>
                <td class="border px-2 py-1">${o.contact||""}</td>
                <td class="border px-2 py-1">${((h=o.visa)==null?void 0:h.visa_type)||""}</td>
                <td class="border px-2 py-1">${((u=o.sticker)==null?void 0:u.sticker)||""}</td>
                <td class="border px-2 py-1">${o.stickerNo||""}</td>
                <td class="border px-2 py-1 text-center">${o.corrFee||0}</td>
                <td class="border px-2 py-1">${o.remarks||""}</td>
                <td class="border px-2 py-1 text-center">${p(t.created_at)}</td>
                 <td class="px-2 py-1 border text-center">
                    ${t.stepId==1?`<button type="button" 
                                class="print-btn bg-blue-500 hover:bg-blue-700 text-white px-2 py-1 rounded"
                                data-id="${t.web_ref||""}">
                                <i class="fas fa-print"></i>
                           </button>`:""}
                </td>
            `,r+="</tr>"}),r+="</tbody></table>",c.innerHTML=r;const e=document.getElementById("latestRecordsTable");e&&new m(e,{responsive:!0,pageLength:100,language:{search:"Search:",lengthMenu:"Show _MENU_ entries per page",zeroRecords:"No matching records found",info:"Showing _START_ to _END_ of _TOTAL_ records",infoEmpty:"No records available",infoFiltered:"(filtered from _MAX_ total records)"}})}function p(d){const s=new Date(d);if(isNaN(s))return"";const r=s.getFullYear(),e=String(s.getMonth()+1).padStart(2,"0"),t=String(s.getDate()).padStart(2,"0"),l=String(s.getHours()).padStart(2,"0"),o=String(s.getMinutes()).padStart(2,"0"),a=String(s.getSeconds()).padStart(2,"0");return`${r}-${e}-${t} ${l}:${o}:${a}`}c.addEventListener("click",function(d){if(d.target.closest(".delete-btn")){d.preventDefault();const r=d.target.closest(".delete-btn").dataset.id;if(n.value,!confirm("Are you sure?"))return;fetch(`/readyDelAct/${r}`,{method:"DELETE",headers:{"X-CSRF-TOKEN":x,Accept:"application/json"}}).then(async e=>{const t=e.headers.get("content-type");if(!t||!t.includes("application/json"))throw new Error("Permission denied");return e.json()}).then(e=>{if(!e.success)throw new Error(e.message||"Delete failed");const t=n?n.value:"";b(e.latest,t),document.getElementById("smsg").textContent="Record deleted",alert(e.message||"Record deleted")}).catch(e=>{alert(e.message)})}if(d.target.closest(".print-btn")){d.preventDefault();const r=d.target.closest(".print-btn").dataset.id;if(!confirm("Are you sure?"))return;window.open(`/app-receive-print/${r}`,"_blank")}if(d.target.closest(".print-btn2")){d.preventDefault();const r=d.target.closest(".print-btn2").dataset.id;if(!confirm("Are you sure?"))return;window.open(`/form-fill-print/${r}`,"_blank")}if(d.target.closest(".print-btn3")){d.preventDefault();const r=d.target.closest(".print-btn3").dataset.id;if(!confirm("Are you sure?"))return;window.open(`/frpReceive-print/${r}`,"_blank")}})});
