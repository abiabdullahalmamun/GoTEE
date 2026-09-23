document.addEventListener("DOMContentLoaded",()=>{const l=document.querySelectorAll(".menu-item"),t=document.querySelector(".detail-sidebar"),d=document.getElementById("menu-title"),u=document.getElementById("child-menu"),s=document.querySelector(".sidebar"),m=document.getElementById("logout-btn"),v=document.querySelector(".menu-toggle"),h=window.menuList||{},g=localStorage.getItem("sidebarOpen")==="true";window.innerWidth<=768&&s.classList.toggle("open",g);function L(){const e=s.classList.toggle("open");localStorage.setItem("sidebarOpen",e),e||t.classList.remove("active")}function f(e,i,c){const n=h[i]||[];if(n.length===0){t.classList.remove("active"),o();return}o(),e.classList.add("active"),d.textContent=c,u.innerHTML=n.map(a=>`
        <li class="has-child"
            data-sub='${JSON.stringify(a)}'>
            
            <a href="${a.url?a.url:"#"}" class="sub-item">
                <i class="${a.icon}"></i> ${a.name}
            </a>

            <ul class="child-container"></ul>
        </li>
    `).join(""),t.classList.add("active"),S()}function S(){document.querySelectorAll(".has-child").forEach(e=>{e.addEventListener("mouseenter",function(){const i=JSON.parse(this.dataset.sub),c=this.querySelector(".child-container");if(!i.child||i.child.length===0){c.innerHTML="";return}c.innerHTML=i.child.map(n=>`
                <li>
                    <a href="${n.url}">
                        <i class="${n.icon}"></i> ${n.name}
                    </a>
                </li>
            `).join("")})})}function o(){l.forEach(e=>e.classList.remove("active"))}function r(){setTimeout(()=>{!s.matches(":hover")&&!t.matches(":hover")&&(t.classList.remove("active"),o(),window.innerWidth<=768&&(s.classList.remove("open"),localStorage.setItem("sidebarOpen",!1)))},200)}v.addEventListener("click",L),l.forEach(e=>e.addEventListener("mouseenter",()=>f(e,e.dataset.menu,e.dataset.title))),s.addEventListener("mouseleave",r),t.addEventListener("mouseleave",r),m.addEventListener("mouseenter",()=>{t.classList.remove("active"),o()})});
