document.addEventListener("DOMContentLoaded",()=>{const a=document.querySelectorAll(".menu-item"),t=document.querySelector(".detail-sidebar"),r=document.getElementById("menu-title"),m=document.getElementById("child-menu"),i=document.querySelector(".sidebar"),u=document.getElementById("logout-btn"),v=document.querySelector(".menu-toggle"),g=window.menuList||{},L=localStorage.getItem("sidebarOpen")==="true";window.innerWidth<=768&&i.classList.toggle("open",L);function h(){const e=i.classList.toggle("open");localStorage.setItem("sidebarOpen",e),e||t.classList.remove("active")}function f(e,S,E){const l=g[S]||[];if(l.length===0){t.classList.remove("active"),s();return}s(),e.classList.add("active"),r.textContent=E,m.innerHTML=l.map(n=>{let d="";return n.child&&n.child.length>0&&(d=`<ul class="submenu-child">
                    ${n.child.map(o=>`
                            <li>
                                <a href="${o.url}">
                                    <i class="${o.icon}"></i> ${o.name}
                                </a>
                            </li>
                        `).join("")}
                </ul>`),`
                <li>
                    <a href="${n.url?n.url:"#"}">
                        <i class="${n.icon}"></i> ${n.name}
                    </a>
                    ${d}
                </li>
            `}).join(""),t.classList.add("active")}function s(){a.forEach(e=>e.classList.remove("active"))}function c(){setTimeout(()=>{!i.matches(":hover")&&!t.matches(":hover")&&(t.classList.remove("active"),s(),window.innerWidth<=768&&(i.classList.remove("open"),localStorage.setItem("sidebarOpen",!1)))},200)}v.addEventListener("click",h),a.forEach(e=>e.addEventListener("mouseenter",()=>f(e,e.dataset.menu,e.dataset.title))),i.addEventListener("mouseleave",c),t.addEventListener("mouseleave",c),u.addEventListener("mouseenter",()=>{t.classList.remove("active"),s()})});
