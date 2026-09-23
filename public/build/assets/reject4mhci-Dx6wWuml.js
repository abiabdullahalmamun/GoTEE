document.addEventListener("DOMContentLoaded",()=>{const r=document.getElementById("web"),c=document.getElementById("latest-records"),l=document.querySelector('meta[name="csrf-token"]').getAttribute("content");r&&r.focus(),r.addEventListener("keydown",function(n){if(n.key==="Enter"){n.preventDefault();const o=this.value.trim(),s=document.getElementById("smsg");if(!o)return;fetch("/reject4mhci",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content"),"X-Requested-With":"XMLHttpRequest"},body:JSON.stringify({web:o})}).then(async t=>{const e=t.headers.get("content-type");if(!e||!e.includes("application/json"))throw new Error("Permission denied");return t.json()}).then(t=>{t.success?(r.value="",document.getElementById("count").textContent=t.count||"0",document.getElementById("smsg").textContent=t.smsg,s.classList.remove("text-red-600"),s.classList.add("text-green-600")):(document.getElementById("smsg").textContent=t.smsg||"Failed to save record.",r.value="",s.classList.remove("text-green-600"),s.classList.add("text-red-600")),d(t.latest)}).catch(t=>{document.getElementById("smsg").textContent=t.message,r.value=""})}});function d(n){if(!n.length){c.innerHTML='<p class="text-gray-500">No recent records.</p>';return}let o=`
            <table class="table-auto w-full border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-2 py-1 border">S.No</th>
                        <th class="px-2 py-1 border">Webfile</th>
                        <th class="px-2 py-1 border">Passport</th>
                        <th class="px-2 py-1 border">Applicant Name</th>
                        <th class="px-2 py-1 border">Contact</th>
                         <th class="px-2 py-1 border">VisaType</th>
                        <th class="px-2 py-1 border">Action</th>
                    </tr>
                </thead>
                <tbody>
        `;n.forEach((s,t)=>{var a;const e=s.webref||{};o+=`
            <tr data-id="${s.id}">
                <td class="px-2 py-1 border text-center">${t+1}</td>
                <td class="px-2 py-1 border">${e.Webfile||""}</td>
                <td class="px-2 py-1 border">${e.passport||""}</td>
                <td class="px-2 py-1 border">${e.ApplicantName||""}</td>
                <td class="px-2 py-1 border">${e.contact||""}</td>
                 <td class="px-2 py-1 border">${((a=e.visa)==null?void 0:a.visa_type)||""}</td>
                <td class="px-2 py-1 border">
                    <button type="button" 
                        class="delete-btn bg-gray-300 hover:bg-red-500 text-white px-2 py-1 rounded"
                        data-id="${s.id}">
                        <i class="fas fa-trash-alt"></i>
                    </button>   
    
                </td>
            </tr>
            `}),o+="</tbody></table>",c.innerHTML=o}c.addEventListener("click",function(n){if(n.target.closest(".delete-btn")){n.preventDefault();const s=n.target.closest(".delete-btn").dataset.id;if(!confirm("Are you sure?"))return;fetch(`/reject4mhci/${s}`,{method:"DELETE",headers:{"X-CSRF-TOKEN":l,Accept:"application/json"}}).then(async t=>{const e=t.headers.get("content-type");if(!e||!e.includes("application/json"))throw new Error("Permission denied");return t.json()}).then(t=>{if(!t.success)throw new Error(t.message||"Delete failed");d(t.latest),document.getElementById("count").textContent=t.count||"0",document.getElementById("smsg").textContent="Record deleted"}).catch(t=>{alert(t.message)})}})});
