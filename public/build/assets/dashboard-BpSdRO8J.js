import{C as c,r as i,a as l}from"./chart-CFoj7Xfn.js";c.register(...i);const p=5e3,u="/api/admin/dashboard/metrics",g=window.API_TOKEN;function o(e,t,r="",s="",a=null){const n=document.querySelector(e);n&&(n.textContent=`${r}${t}${s}`,a&&(n.classList.remove("text-green-500","text-red-500"),n.classList.add(a)))}function h(e){const t={month:"long",day:"numeric",year:"numeric",hour:"numeric",minute:"2-digit",hour12:!0};return e.toLocaleString("en-US",t)}function f(e){o(".current-time",h(new Date),"Last updated: "),o(".total-receive",e.totalReceive),o(".last-updated",new Date().toLocaleString(),"Last updated: ");const t=document.getElementById("logTableBody");t&&(t.innerHTML="",e.logs&&e.logs.length>0?e.logs.forEach((r,s)=>{const a=`
                <tr class="border-b hover:bg-gray-50">

                    <td class="px-2 py-1 text-center">${s+1}</td>
                    <td class="px-2 py-1 break-all">
                        ${r.api}
                    </td>

                    <td class="px-2 py-1">
                        <pre class="text-xs whitespace-pre-wrap">${JSON.stringify(r.payload,null,2)}</pre>
                    </td>

                    <td class="px-2 py-1">
                        <pre class="text-xs whitespace-pre-wrap">${JSON.stringify(r.response,null,2)}</pre>
                    </td>

                </tr>
            `;t.insertAdjacentHTML("beforeend",a)}):t.innerHTML=`
            <tr>
                <td colspan="4" class="text-center py-4 text-gray-500">
                    No logs found
                </td>
            </tr>
        `)}async function d(){try{const e=await l.get(u,{headers:{Authorization:`Bearer ${g}`}});f(e.data),console.log("Dashboard refreshed at",new Date().toLocaleTimeString())}catch(e){console.error("Failed to refresh dashboard:",e)}}document.addEventListener("DOMContentLoaded",()=>{d(),setInterval(d,p)});
