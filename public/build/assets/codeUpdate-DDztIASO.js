document.addEventListener("DOMContentLoaded",()=>{const o=document.getElementById("web"),a=document.getElementById("latest-records"),i=document.querySelector('meta[name="csrf-token"]').getAttribute("content");o&&o.focus(),o.addEventListener("keydown",function(s){if(s.key==="Enter"){s.preventDefault();const n=this.value.trim();if(!n)return;const e=document.getElementById("smsg");fetch("/codeUpdate",{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').getAttribute("content"),"X-Requested-With":"XMLHttpRequest"},body:JSON.stringify({web:n})}).then(async t=>{const r=t.headers.get("content-type");if(!r||!r.includes("application/json"))throw new Error("Permission denied");return t.json()}).then(t=>{t.success?(o.value="",l(t.latest),document.getElementById("count").textContent=t.count||"0",document.getElementById("smsg").textContent=t.smsg,e.classList.remove("text-red-600"),e.classList.add("text-green-600")):(document.getElementById("smsg").textContent=t.smsg||"Failed to get record.",o.value="",e.classList.remove("text-green-600"),e.classList.add("text-red-600"))}).catch(t=>{document.getElementById("smsg").textContent=t.message,o.value="",e.classList.remove("text-green-600"),e.classList.add("text-red-600")})}});function l(s){if(!s.length){a.innerHTML='<p class="text-gray-500">No recent records.</p>';return}let n=`
            <table class="table-auto w-full border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-2 py-1 border">S.No</th>
                        <th class="px-2 py-1 border">Token</th>
                        <th class="px-2 py-1 border">Sevrice</th>
                        <th class="px-2 py-1 border">Webfile</th>
                        <th class="px-2 py-1 border">Code</th>
                        <th class="px-2 py-1 border">Status</th>
                        <th class="px-2 py-1 border">CreatedAt</th>
                        <th class="px-2 py-1 border">Counter</th>
                        <th class="px-2 py-1 border">ServedBy</th>
                        <th class="px-2 py-1 border">Action</th>
                    </tr>
                </thead>
                <tbody>
        `;s.forEach((e,t)=>{var r,d,c;e.webref,n+=`
            <tr data-id="${e.id}">
                <td class="px-2 py-1 border text-center">${t+1}</td>
                <td class="px-2 py-1 border text-center">${e.token||""}</td>
                <td class="px-2 py-1 border text-center">${e.service.service_name||""}</td>
                <td class="px-2 py-1 border text-center">${e.web||""}</td>
                <td class="px-2 py-1 border text-center">${e.random||""}</td>
                <td class="px-2 py-1 border text-center">${e.status==1?"Active":"Inactive"}</td>
                <td class="px-2 py-1 border text-center">${p(e.created_at)}</td>
                <td class="px-2 py-1 border text-center">${((r=e.tokenlog)==null?void 0:r.cno)||""}</td>
                <td class="px-2 py-1 border text-center">${((c=(d=e.tokenlog)==null?void 0:d.user)==null?void 0:c.name)||""}</td>
                <td class="px-2 py-1 border text-center">
                    <button type="button" 
                        class="delete-btn bg-gray-300 hover:bg-red-500 text-white px-2 py-1 rounded"
                        data-id="${e.id}">
                        <i class="fas fa-edit"></i>
                    </button>   
    
                </td>
            </tr>
            `}),n+="</tbody></table>",a.innerHTML=n}function p(s){const n=new Date(s);if(isNaN(n))return"";const e=n.getFullYear(),t=String(n.getMonth()+1).padStart(2,"0"),r=String(n.getDate()).padStart(2,"0"),d=String(n.getHours()).padStart(2,"0"),c=String(n.getMinutes()).padStart(2,"0"),u=String(n.getSeconds()).padStart(2,"0");return`${e}-${t}-${r} ${d}:${c}:${u}`}a.addEventListener("click",function(s){if(s.target.closest(".delete-btn")){s.preventDefault();const e=s.target.closest(".delete-btn").dataset.id;if(!confirm("Are you sure?"))return;fetch(`/codeUpdate/${e}`,{method:"DELETE",headers:{"X-CSRF-TOKEN":i,Accept:"application/json"}}).then(async t=>{const r=t.headers.get("content-type");if(!r||!r.includes("application/json"))throw new Error("Permission denied");return t.json()}).then(t=>{if(!t.success)throw new Error(t.message||"update failed");l(t.latest),document.getElementById("count").textContent=t.count||"0",document.getElementById("smsg").textContent=t.message}).catch(t=>{alert(t.message)})}})});
