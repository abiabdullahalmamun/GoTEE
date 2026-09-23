document.addEventListener("DOMContentLoaded",()=>{const r=document.getElementById("web"),o=document.getElementById("latest-records");document.querySelector('meta[name="csrf-token"]').getAttribute("content"),r&&r.focus(),r.addEventListener("keydown",function(n){if(n.key==="Enter"){n.preventDefault();const d=this.value.trim();if(!d)return;const t=document.getElementById("smsg");fetch("/web-replace",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content"),"X-Requested-With":"XMLHttpRequest"},body:JSON.stringify({web:d})}).then(async e=>{const s=e.headers.get("content-type");if(!s||!s.includes("application/json"))throw new Error("Permission denied");return e.json()}).then(e=>{e.success?(r.value="",l(e.latest),document.getElementById("smsg").textContent=e.smsg,t.classList.remove("text-red-600"),t.classList.add("text-green-600")):(document.getElementById("smsg").textContent=e.smsg||"Failed to save record.",r.value="",t.classList.remove("text-green-600"),t.classList.add("text-red-600"))}).catch(e=>{document.getElementById("smsg").textContent=e.message,r.value="",t.classList.remove("text-green-600"),t.classList.add("text-red-600")})}});function l(n){if(!n.length){o.innerHTML='<p class="text-gray-500">No recent records.</p>';return}let d=`
            <table class="table-auto w-full border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-2 py-1 border">S.No</th>
                        <th class="px-2 py-1 border">Webfile</th>
                        <th class="px-2 py-1 border">Passport</th>
                        <th class="px-2 py-1 border">Applicant Name</th>
                        <th class="px-2 py-1 border">Contact</th>
                        <th class="px-2 py-1 border">VisaType</th>
                        <th class="px-2 py-1 border">SticketType</th>
                        <th class="px-2 py-1 border">StickerNo</th>
                     <th class="px-2 py-1 border">Action</th>
                      
                    </tr>
                </thead>
                <tbody>
        `;n.forEach((t,e)=>{var c,a;const s=t.webref||{};d+=`
            <tr data-id="${t.id}">
                <td class="px-2 py-1 border text-center">${e+1}</td>
                <td class="px-2 py-1 border">${s.Webfile||""}</td>
                <td class="px-2 py-1 border">${s.passport||""}</td>
                <td class="px-2 py-1 border">${s.ApplicantName||""}</td>
                <td class="px-2 py-1 border">${s.contact||""}</td>
                 <td class="border px-2 py-1">${((c=s.visa)==null?void 0:c.visa_type)||""}</td>
                <td class="border px-2 py-1">${((a=s.sticker)==null?void 0:a.sticker)||""}</td>
                <td class="border px-2 py-1">${s.stickerNo||""}</td>
                  <td class="px-2 py-1 border text-center">
                   <a href="/web-replace/${t.web_ref}" class="fa-solid fa-file-pen">
                        Edit
                    </a> 

                </td>
       
            </tr>
            `}),d+="</tbody></table>",o.innerHTML=d}window.location.href=`/web-replace/${id}`});
