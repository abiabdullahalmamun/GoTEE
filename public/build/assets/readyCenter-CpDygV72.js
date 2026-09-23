document.addEventListener("DOMContentLoaded",()=>{const o=document.getElementById("web"),c=document.getElementById("latest-records"),a=document.querySelector('meta[name="csrf-token"]').getAttribute("content");o&&o.focus(),o.addEventListener("keydown",function(s){if(s.key==="Enter"){s.preventDefault();const r=this.value.trim();if(!r)return;const t=document.getElementById("smsg");fetch("/readyCenter",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content"),"X-Requested-With":"XMLHttpRequest"},body:JSON.stringify({web:r})}).then(async e=>{const n=e.headers.get("content-type");if(!n||!n.includes("application/json"))throw new Error("Permission denied");return e.json()}).then(e=>{e.success?(o.value="",d(e.latest),document.getElementById("count").textContent=e.count||"0",document.getElementById("smsg").textContent=e.smsg,t.classList.remove("text-red-600"),t.classList.add("text-green-600")):(document.getElementById("smsg").textContent=e.smsg||"Failed to save record.",o.value="",t.classList.remove("text-green-600"),t.classList.add("text-red-600"))}).catch(e=>{document.getElementById("smsg").textContent=e.message,o.value="",t.classList.remove("text-green-600"),t.classList.add("text-red-600")})}});function d(s){if(!s.length){c.innerHTML='<p class="text-gray-500">No recent records.</p>';return}let r=`
            <table class="table-auto w-full border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-2 py-1 border">S.No</th>
                        <th class="px-2 py-1 border">Webfile</th>
                        <th class="px-2 py-1 border">Passport</th>
                        <th class="px-2 py-1 border">Applicant Name</th>
                        <th class="px-2 py-1 border">Contact</th>
                      
                    </tr>
                </thead>
                <tbody>
        `;s.forEach((t,e)=>{const n=t.webref||{};r+=`
            <tr data-id="${t.id}">
                <td class="px-2 py-1 border text-center">${e+1}</td>
                <td class="px-2 py-1 border">${n.Webfile||""}</td>
                <td class="px-2 py-1 border">${n.passport||""}</td>
                <td class="px-2 py-1 border">${n.ApplicantName||""}</td>
                <td class="px-2 py-1 border">${n.contact||""}</td>
       
            </tr>
            `}),r+="</tbody></table>",c.innerHTML=r}c.addEventListener("click",function(s){if(s.target.closest(".delete-btn")){s.preventDefault();const t=s.target.closest(".delete-btn").dataset.id;if(!confirm("Are you sure?"))return;fetch(`/readyCenter/${t}`,{method:"DELETE",headers:{"X-CSRF-TOKEN":a,Accept:"application/json"}}).then(async e=>{const n=e.headers.get("content-type");if(!n||!n.includes("application/json"))throw new Error("Permission denied");return e.json()}).then(e=>{if(!e.success)throw new Error(e.message||"Delete failed");d(e.latest),document.getElementById("count").textContent=e.count||"0",document.getElementById("smsg").textContent="Record deleted"}).catch(e=>{alert(e.message)})}})});
