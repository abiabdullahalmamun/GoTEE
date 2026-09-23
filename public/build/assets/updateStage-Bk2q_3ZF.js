document.addEventListener("DOMContentLoaded",()=>{const r=document.getElementById("web"),c=document.getElementById("latest-records"),l=document.querySelector('meta[name="csrf-token"]').getAttribute("content");r&&r.focus(),r.addEventListener("keydown",function(n){if(n.key==="Enter"){n.preventDefault();const o=this.value.trim();if(!o)return;fetch("/updateStage",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content"),"X-Requested-With":"XMLHttpRequest"},body:JSON.stringify({web:o})}).then(async e=>{const s=e.headers.get("content-type");if(!s||!s.includes("application/json"))throw new Error("Permission denied");return e.json()}).then(e=>{e.success?(r.value="",a(e.latest),document.getElementById("smsg").textContent=e.smsg):(document.getElementById("smsg").textContent=e.smsg||"Failed to save record.",r.value="")}).catch(e=>{document.getElementById("smsg").textContent=e.message,r.value=""})}});function a(n){if(!n.length){c.innerHTML='<p class="text-gray-500">No recent records.</p>';return}let o=`
            <table class="table-auto w-full border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-2 py-1 border">S.No</th>
                        <th class="px-2 py-1 border">Webfile</th>
                        <th class="px-2 py-1 border">Passport</th>
                        <th class="px-2 py-1 border">Applicant Name</th>
                        <th class="px-2 py-1 border">Contact</th>
                        <th class="px-2 py-1 border">Stage</th>
                        <th class="px-2 py-1 border">Action</th>
                    </tr>
                </thead>
                <tbody>
        `;n.forEach((e,s)=>{const t=e.webref||{},d={1:"Received At Center",2:"Sent to HCI",31:"Received from HCI",32:"Received from HCI",33:"Received from HCI",11:"Biometric",4:"Ready At Center",5:"Delivered"};o+=`
            <tr data-id="${e.id}">
                <td class="px-2 py-1 border text-center">${s+1}</td>
                <td class="px-2 py-1 border">${t.Webfile||""}</td>
                <td class="px-2 py-1 border">${t.passport||""}</td>
                <td class="px-2 py-1 border">${t.ApplicantName||""}</td>
                <td class="px-2 py-1 border">${t.contact||""}</td>
                <td class="px-2 py-1 border">${d[e.stepId]||""}</td>
             
                <td class="px-2 py-1 border text-center">
                   ${s===0?`
                    <button type="button" 
                        class="delete-btn bg-gray-300 hover:bg-red-500 text-white px-2 py-1 rounded"
                        data-id="${e.id}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                `:""}
                </td>
            </tr>
            `}),o+="</tbody></table>",c.innerHTML=o}c.addEventListener("click",function(n){if(n.target.closest(".delete-btn")){n.preventDefault();const e=n.target.closest(".delete-btn").dataset.id,s=document.getElementById("smsg");if(!confirm("Are you sure?"))return;fetch(`/updateStage/${e}`,{method:"DELETE",headers:{"X-CSRF-TOKEN":l,Accept:"application/json"}}).then(async t=>{const d=t.headers.get("content-type");if(!d||!d.includes("application/json"))throw new Error("Permission denied");return t.json()}).then(t=>{t.success?(r.value="",document.getElementById("smsg").textContent=t.message,s.classList.remove("text-red-600"),s.classList.add("text-green-600")):(document.getElementById("smsg").textContent=t.message||"Failed to save record.",r.value="",s.classList.remove("text-green-600"),s.classList.add("text-red-600")),a(t.latest)}).catch(t=>{alert(t.message)})}})});
