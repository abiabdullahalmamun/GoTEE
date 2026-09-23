document.addEventListener("DOMContentLoaded",()=>{const r=document.getElementById("web");document.getElementById("remarks");const c=document.getElementById("latest-records"),l=document.querySelector('meta[name="csrf-token"]').getAttribute("content");r&&r.focus(),r.addEventListener("keydown",function(n){if(n.key==="Enter"){n.preventDefault();const o="Send2MOFA",s=this.value.trim(),t=document.getElementById("smsg");if(!s)return;fetch("/centerDelivery",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content"),"X-Requested-With":"XMLHttpRequest"},body:JSON.stringify({web:s,rmks:o})}).then(async e=>{const a=e.headers.get("content-type");if(!a||!a.includes("application/json"))throw new Error("Permission denied");return e.json()}).then(e=>{e.success?(r.value="",d(e.latest),document.getElementById("count").textContent=e.count||"0",document.getElementById("smsg").textContent=e.smsg,t.classList.remove("text-red-600"),t.classList.add("text-green-600")):(document.getElementById("smsg").textContent=e.smsg||"Failed to save record.",r.value="",t.classList.remove("text-green-600"),t.classList.add("text-red-600")),document.getElementById("remarks").value="Send2MOFA"}).catch(e=>{document.getElementById("smsg").textContent=e.message,r.value="",document.getElementById("remarks").value="Send2MOFA"})}});function d(n){if(!n.length){c.innerHTML='<p class="text-gray-500">No recent records.</p>';return}let o=`
            <table class="table-auto w-full border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-2 py-1 border">S.No</th>
                        <th class="px-2 py-1 border">Webfile</th>
                        <th class="px-2 py-1 border">Passport</th>
                        <th class="px-2 py-1 border">Applicant Name</th>
                        <th class="px-2 py-1 border">Contact</th>
                        <th class="px-2 py-1 border">Remarks</th>
                    </tr>
                </thead>
                <tbody>
        `;n.forEach((s,t)=>{const e=s.webref||{};o+=`
            <tr data-id="${s.id}">
                <td class="px-2 py-1 border text-center">${t+1}</td>
                <td class="px-2 py-1 border text-center">${e.Webfile||""}</td>
                <td class="px-2 py-1 border text-center">${e.passport||""}</td>
                <td class="px-2 py-1 border text-center">${e.ApplicantName||""}</td>
                <td class="px-2 py-1 border text-center">${e.contact||""}</td>
                 <td class="px-2 py-1 border text-center">${s.remarks||""}</td>
              
            </tr>
            `}),o+="</tbody></table>",c.innerHTML=o}c.addEventListener("click",function(n){if(n.target.closest(".delete-btn")){n.preventDefault();const s=n.target.closest(".delete-btn").dataset.id;if(!confirm("Are you sure?"))return;fetch(`/centerDelivery/${s}`,{method:"DELETE",headers:{"X-CSRF-TOKEN":l,Accept:"application/json"}}).then(async t=>{const e=t.headers.get("content-type");if(!e||!e.includes("application/json"))throw new Error("Permission denied");return t.json()}).then(t=>{if(!t.success)throw new Error(t.message||"Delete failed");d(t.latest),document.getElementById("count").textContent=t.count||"0",document.getElementById("smsg").textContent="Record deleted"}).catch(t=>{alert(t.message)})}})});
