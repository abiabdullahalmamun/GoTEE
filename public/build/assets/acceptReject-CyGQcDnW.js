document.addEventListener("DOMContentLoaded",()=>{const o=document.getElementById("latest-records"),l=document.querySelector('meta[name="csrf-token"]').getAttribute("content"),a=document.getElementById("actId");p(),a&&a.addEventListener("change",function(){p(this.value)});function p(s=""){const n=s;fetch("/acceptRejectAct",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content"),"X-Requested-With":"XMLHttpRequest"},body:JSON.stringify({act:n})}).then(async t=>{const e=t.headers.get("content-type");if(!e||!e.includes("application/json"))throw new Error("Permission denied");return t.json()}).then(t=>{t.success?(i(t.latest),document.getElementById("count").textContent=t.count||"0",document.getElementById("smsg").textContent=t.smsg):document.getElementById("smsg").textContent=t.smsg||"Failed to save record."}).catch(t=>{document.getElementById("smsg").textContent=t.message})}function i(s){if(!s.length){o.innerHTML='<p class="text-gray-500">No recent records.</p>';return}let n=`
            <table class="table-auto w-full border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-2 py-1 border">S.No</th>
                        <th class="px-2 py-1 border">Webfile</th>
                        <th class="px-2 py-1 border">Passport</th>
                        <th class="px-2 py-1 border">Applicant Name</th>
                        <th class="px-2 py-1 border">Contact</th>
                         <th class="px-2 py-1 border">VisaType</th>
                        <th class="px-2 py-1 border">Stage</th>
                        <th class="px-2 py-1 border">EventTime</th>
                        <th class="px-2 py-1 border">Action</th>
                    </tr>
                </thead>
                <tbody>
        `;s.forEach((t,e)=>{var c;const r=t.webref||{},d={1:"Received At Center",2:"Sent to HCI",31:"Received from HCI",32:"Received from HCI",33:"Received from HCI",4:"Ready At Center",5:"Delivered"};n+=`
            <tr data-id="${t.id}" class="${t.stepId==31?"text-gray-500":t.stepId==32?"text-red-500":t.stepId==33?"text-blue-500":""}">
                <td class="px-2 py-1 border text-center">${e+1}</td>
                <td class="px-2 py-1 border text-center">${r.Webfile||""}</td>
                <td class="px-2 py-1 border">${r.passport||""}</td>
                <td class="px-2 py-1 border">${r.ApplicantName||""}</td>
                <td class="px-2 py-1 border">${r.contact||""}</td>
                  <td class="px-2 py-1 border">${((c=r.visa)==null?void 0:c.visa_type)||""}</td>
                <td class="px-2 py-1 border">${d[t.stepId]||""}</td>
                <td class="px-2 py-1 border text-center">${u(t.created_at)}</td>
                <td class="px-2 py-1 border text-center">
                    <button type="button" 
                        class="delete-btn bg-gray-300 hover:bg-red-500 text-white px-2 py-1 rounded"
                        data-id="${t.id}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
            `}),n+="</tbody></table>",o.innerHTML=n}function u(s){const n=new Date(s);if(isNaN(n))return"";const t=n.getFullYear(),e=String(n.getMonth()+1).padStart(2,"0"),r=String(n.getDate()).padStart(2,"0"),d=String(n.getHours()).padStart(2,"0"),c=String(n.getMinutes()).padStart(2,"0"),y=String(n.getSeconds()).padStart(2,"0");return`${t}-${e}-${r} ${d}:${c}:${y}`}o.addEventListener("click",function(s){if(s.target.closest(".delete-btn")){s.preventDefault();const t=s.target.closest(".delete-btn").dataset.id;if(!confirm("Are you sure?"))return;fetch(`/acceptRejectAct/${t}`,{method:"DELETE",headers:{"X-CSRF-TOKEN":l,Accept:"application/json"}}).then(async e=>{const r=e.headers.get("content-type");if(!r||!r.includes("application/json"))throw new Error("Permission denied");return e.json()}).then(e=>{if(!e.success)throw new Error(e.message||"Delete failed");i(e.latest),document.getElementById("smsg").textContent="Record deleted"}).catch(e=>{alert(e.message)})}})});
