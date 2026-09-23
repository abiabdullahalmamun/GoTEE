document.addEventListener("DOMContentLoaded",()=>{const d=document.getElementById("web"),o=document.getElementById("latest-records"),p=document.querySelector('meta[name="csrf-token"]').getAttribute("content");d&&d.focus(),d.addEventListener("keydown",function(r){if(r.key==="Enter"){r.preventDefault();const n=this.value.trim();if(!n)return;const t=document.getElementById("smsg");fetch("/readyDelDelete",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content"),"X-Requested-With":"XMLHttpRequest"},body:JSON.stringify({web:n})}).then(async e=>{const s=e.headers.get("content-type");if(!s||!s.includes("application/json"))throw new Error("Permission denied");return e.json()}).then(e=>{e.success?(d.value="",c(e.latest),document.getElementById("count").textContent=e.count||"0",document.getElementById("smsg").textContent=e.smsg,t.classList.remove("text-red-600"),t.classList.add("text-green-600")):(document.getElementById("smsg").textContent=e.smsg||"Failed to save record.",d.value="",t.classList.remove("text-green-600"),t.classList.add("text-red-600"))}).catch(e=>{document.getElementById("smsg").textContent=e.message,d.value="",t.classList.remove("text-green-600"),t.classList.add("text-red-600")})}});function c(r){if(!r.length){o.innerHTML='<p class="text-gray-500">No recent records.</p>';return}let n=`
            <table class="table-auto w-full border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-2 py-1 border">S.No</th>
                        <th class="px-2 py-1 border">Webfile</th>
                        <th class="px-2 py-1 border">Passport</th>
                        <th class="px-2 py-1 border">Applicant Name</th>
                        <th class="px-2 py-1 border">Contact</th>
                        <th class="px-2 py-1 border">Visatype</th>
                         <th class="px-2 py-1 border">St_Type</th> 
                         <th class="px-2 py-1 border">Sticker</th>
                         <th class="px-2 py-1 border">Correction</th>
                          <th class="px-2 py-1 border">User</th>
                        <th class="px-2 py-1 border">Step</th>
                         <th class="px-2 py-1 border">Remarks</th>
                        <th class="px-2 py-1 border">Action</th>
                    </tr>
                </thead>
                <tbody>
        `;r.forEach((t,e)=>{var a,l;const s=t.webref||{};n+=`
            <tr data-id="${t.id}">
                <td class="px-2 py-1 border text-center">${e+1}</td>
                <td class="px-2 py-1 border">${s.Webfile||""}</td>
                <td class="px-2 py-1 border">${s.passport||""}</td>
                <td class="px-2 py-1 border">${s.ApplicantName||""}</td>
                <td class="px-2 py-1 border">${s.contact||""}</td>
                    <td class="px-2 py-1 border">${((a=s.visa)==null?void 0:a.visa_type)||""}</td>
                <td class="px-2 py-1 border">${((l=s.sticker)==null?void 0:l.sticker)||""}</td> 
                 <td class="px-2 py-1 border">${s.stickerNo||""}</td>
                   <td class="px-2 py-1 border">${s.corrFee||"0"}</td>
                 <td class="px-2 py-1 border">${t.user?t.user.name:""}</td>

                <td class="px-2 py-1 border"> ${t.stepId===4?"ReadyCenter":t.stepId===5?"Delivery":t.stepId===11?"Biometric":t.stepId===8?"DocumentReceived":t.stepId===9?"DVDCreated":""}</td>
                 <td class="px-2 py-1 border">
                    <span class="${t.remarks==="Send2MOFA"?"text-red-600":""}">
                        ${t.remarks}
                    </span>
                </td>

                <td class="px-2 py-1 border">
                    <button type="button" 
                        class="delete-btn bg-gray-300 hover:bg-red-500 text-white px-2 py-1 rounded"
                        data-id="${t.id}">
                        <i class="fas fa-trash-alt"></i>
                    </button>   
    
                </td>
            </tr>
            `}),n+="</tbody></table>",o.innerHTML=n}o.addEventListener("click",function(r){if(r.target.closest(".delete-btn")){r.preventDefault();const t=r.target.closest(".delete-btn").dataset.id;if(!confirm("Are you sure?"))return;fetch(`/readyDelDelete/${t}`,{method:"DELETE",headers:{"X-CSRF-TOKEN":p,Accept:"application/json"}}).then(async e=>{const s=e.headers.get("content-type");if(!s||!s.includes("application/json"))throw new Error("Permission denied");return e.json()}).then(e=>{if(!e.success)throw new Error(e.message||"Delete failed");c(e.latest),document.getElementById("count").textContent=e.count||"0",document.getElementById("smsg").textContent=e.message||""}).catch(e=>{alert(e.message)})}})});
