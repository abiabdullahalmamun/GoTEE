document.addEventListener("DOMContentLoaded",()=>{const r=document.querySelectorAll(".menu-item"),t=document.querySelector(".detail-sidebar"),d=document.getElementById("menu-title"),u=document.getElementById("child-menu"),i=document.querySelector(".sidebar"),m=document.getElementById("logout-btn"),h=document.querySelector(".menu-toggle"),v=window.menuList||{};localStorage.getItem("sidebarOpen"),i&&window.innerWidth<=768&&i.classList.remove("open");function f(){const e=document.querySelector("aside.sidebar");e&&e.classList.toggle("open")}h.addEventListener("click",function(e){e.preventDefault(),f()});function L(e,s,c){const n=v[s]||[];if(n.length===0){t.classList.remove("active"),o();return}o(),e.classList.add("active"),d.textContent=c,u.innerHTML=n.map(a=>`
        <li class="has-child"
            data-sub='${JSON.stringify(a)}'>
            
            <a href="${a.url?a.url:"#"}" class="sub-item">
                <i class="${a.icon}"></i> ${a.name}
            </a>

            <ul class="child-container"></ul>
        </li>
    `).join(""),t.classList.add("active"),g()}function g(){document.querySelectorAll(".has-child").forEach(e=>{e.addEventListener("mouseenter",function(){const s=JSON.parse(this.dataset.sub),c=this.querySelector(".child-container");if(!s.child||s.child.length===0){c.innerHTML="";return}c.innerHTML=s.child.map(n=>`
                <li>
                    <a href="${n.url}">
                        <i class="${n.icon}"></i> ${n.name}
                    </a>
                </li>
            `).join("")})})}function o(){r.forEach(e=>e.classList.remove("active"))}function l(){window.innerWidth<=768||setTimeout(()=>{i&&t&&!i.matches(":hover")&&!t.matches(":hover")&&(t.classList.remove("active"),o())},200)}r.forEach(e=>e.addEventListener("mouseenter",()=>L(e,e.dataset.menu,e.dataset.title))),i.addEventListener("mouseleave",l),t.addEventListener("mouseleave",l),m.addEventListener("mouseenter",()=>{t.classList.remove("active"),o()})});
