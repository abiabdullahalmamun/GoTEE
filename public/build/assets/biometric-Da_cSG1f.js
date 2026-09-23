document.addEventListener("DOMContentLoaded",()=>{const r=document.getElementById("web"),i=document.getElementById("remarks"),d=document.getElementById("latest-records"),m=document.querySelector('meta[name="csrf-token"]').getAttribute("content");r&&r.focus(),r.addEventListener("keydown",function(s){if(s.key==="Enter"){s.preventDefault();const o=i.value.trim(),n=this.value.trim();if(!n)return;const t=document.getElementById("smsg");fetch("/biometric",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content"),"X-Requested-With":"XMLHttpRequest"},body:JSON.stringify({web:n,rmks:o})}).then(async e=>{const c=e.headers.get("content-type");if(!c||!c.includes("application/json"))throw new Error("Permission denied");return e.json()}).then(e=>{e.success?(r.value="",a(e.latest),document.getElementById("count").textContent=e.count||"0",document.getElementById("smsg").textContent=e.smsg,t.classList.remove("text-red-600"),t.classList.add("text-green-600")):(document.getElementById("smsg").textContent=e.smsg||"Failed to save record.",r.value="",t.classList.remove("text-green-600"),t.classList.add("text-red-600")),document.getElementById("remarks").value=""}).catch(e=>{document.getElementById("smsg").textContent=e.message,r.value="",t.classList.remove("text-green-600"),t.classList.add("text-red-600"),document.getElementById("remarks").value=""})}});function a(s){if(!s.length){d.innerHTML='<p class="text-gray-500">No recent records.</p>';return}let o=`
            <table class="table-auto w-full border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-2 py-1 border">S.No</th>
                        <th class="px-2 py-1 border">Webfile</th>
                        <th class="px-2 py-1 border">Passport</th>
                        <th class="px-2 py-1 border">Applicant Name</th>
                        <th class="px-2 py-1 border">Contact</th>
                        <th class="px-2 py-1 border">VisaType</th>
                        <th class="px-2 py-1 border">StcType</th>
                        <th class="px-2 py-1 border">Sticker</th>
                        <th class="px-2 py-1 border">Remarks</th>
                    </tr>
                </thead>
                <tbody>
        `;s.forEach((n,t)=>{var c,l;const e=n.webref||{};o+=`
            <tr data-id="${n.id}">
                <td class="px-2 py-1 border text-center">${t+1}</td>
                <td class="px-2 py-1 border">${e.Webfile||""}</td>
                <td class="px-2 py-1 border">${e.passport||""}</td>
                <td class="px-2 py-1 border">${e.ApplicantName||""}</td>
                <td class="px-2 py-1 border">${e.contact||""}</td>
                <td class="px-2 py-1 border"> ${((c=e==null?void 0:e.visa)==null?void 0:c.visa_type)||""}</td>
                <td class="px-2 py-1 border">${((l=e==null?void 0:e.sticker)==null?void 0:l.sticker)||""}</td>
                <td class="px-2 py-1 border">${e.stickerNo||""}</td>
                <td class="px-2 py-1 border text-center">${n.remarks||""}</td>
            </tr>
            `}),o+="</tbody></table>",d.innerHTML=o}d.addEventListener("click",function(s){if(s.target.closest(".delete-btn")){s.preventDefault();const n=s.target.closest(".delete-btn").dataset.id;if(!confirm("Are you sure?"))return;fetch(`/biometric/${n}`,{method:"DELETE",headers:{"X-CSRF-TOKEN":m,Accept:"application/json"}}).then(async t=>{const e=t.headers.get("content-type");if(!e||!e.includes("application/json"))throw new Error("Permission denied");return t.json()}).then(t=>{if(!t.success)throw new Error(t.message||"Delete failed");a(t.latest),document.getElementById("count").textContent=t.count||"0",document.getElementById("smsg").textContent="Record deleted"}).catch(t=>{alert(t.message)})}})});
