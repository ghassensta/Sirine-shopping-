{{--
    ========================================================
    🤖 Assistant IA Sirine Shopping — GROQ (100% Gratuit)
    ========================================================
    INSTALLATION :
    1. resources/views/partials/ai-chatbot.blade.php
    2. Dans layouts/app.blade.php avant </body> :
       @include('partials.ai-chatbot')
    3. Dans .env :
       GROQ_API_KEY=gsk_xxxxxxxxxxxx
    4. routes/web.php :
       Route::post('/api/chat', [ChatController::class, 'chat'])->name('chat');
    ========================================================
--}}

<style>
  @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Jost:wght@300;400;500&display=swap');

  :root {
    --gold: #C9A96E;
    --gold-light: #E8D5A3;
    --gold-dark: #8B6914;
    --cream: #FAF8F3;
    --dark: #1A1410;
  }

  #sc-bubble {
    position: fixed;
    bottom: 94px;
    right: 24px;
    z-index: 99999;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 10px;
    font-family: 'Jost', sans-serif;
  }

  #sc-hint {
    background: var(--dark);
    color: var(--gold-light);
    padding: 9px 15px;
    border-radius: 20px 20px 4px 20px;
    font-size: 13px;
    font-weight: 300;
    letter-spacing: 0.4px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.25);
    border: 1px solid rgba(201,169,110,0.3);
    cursor: pointer;
    white-space: nowrap;
    animation: scFadeUp 0.5s ease forwards;
  }

  #sc-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--gold-dark), var(--gold), var(--gold-light));
    border: none;
    cursor: pointer;
    box-shadow: 0 6px 24px rgba(201,169,110,0.55);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s, box-shadow 0.3s;
    position: relative;
  }

  #sc-toggle:hover { transform: scale(1.08); box-shadow: 0 8px 32px rgba(201,169,110,0.7); }
  #sc-toggle svg { width: 25px; height: 25px; fill: white; transition: opacity 0.2s; }
  #sc-toggle .ic-close { display: none; }
  #sc-toggle.open .ic-chat { display: none; }
  #sc-toggle.open .ic-close { display: block; }

  #sc-toggle::after {
    content: '';
    position: absolute;
    top: 5px; right: 5px;
    width: 10px; height: 10px;
    background: #ff4757;
    border-radius: 50%;
    border: 2px solid white;
    animation: scPulse 2s infinite;
  }
  #sc-toggle.open::after { display: none; }

  #sc-window {
    position: fixed;
    bottom: 100px;
    right: 24px;
    width: 370px;
    height: 510px;
    background: var(--cream);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.18), 0 0 0 1px rgba(201,169,110,0.2);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 99998;
    transform: scale(0.85) translateY(20px);
    opacity: 0;
    pointer-events: none;
    transform-origin: bottom right;
    transition: transform 0.35s cubic-bezier(0.34,1.56,0.64,1), opacity 0.25s ease;
  }
  #sc-window.open { transform: scale(1) translateY(0); opacity: 1; pointer-events: all; }

  .sc-header {
    background: linear-gradient(135deg, var(--dark) 0%, #2A1F14 100%);
    padding: 16px 18px;
    display: flex; align-items: center; gap: 11px;
    border-bottom: 1px solid rgba(201,169,110,0.2);
    flex-shrink: 0;
  }
  .sc-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg, var(--gold-dark), var(--gold));
    display: flex; align-items: center; justify-content: center;
    font-size: 17px; flex-shrink: 0;
    border: 2px solid rgba(201,169,110,0.35);
  }
  .sc-name {
    font-family: 'Cormorant Garamond', serif;
    font-size: 16px; font-weight: 600;
    color: var(--gold-light); letter-spacing: 0.4px;
  }
  .sc-status {
    font-size: 11px; color: rgba(232,213,163,0.55);
    font-weight: 300; display: flex; align-items: center; gap: 5px; margin-top: 2px;
  }
  .sc-status::before {
    content: ''; display: inline-block;
    width: 6px; height: 6px; background: #2ecc71; border-radius: 50%;
    animation: scPulse 2s infinite;
  }

  .sc-messages {
    flex: 1; overflow-y: auto;
    padding: 16px 14px;
    display: flex; flex-direction: column; gap: 11px;
    scroll-behavior: smooth;
  }
  .sc-messages::-webkit-scrollbar { width: 3px; }
  .sc-messages::-webkit-scrollbar-thumb { background: var(--gold-light); border-radius: 2px; }

  .sc-msg { display: flex; gap: 7px; animation: scMsgIn 0.3s ease forwards; }
  .sc-msg.user { flex-direction: row-reverse; }
  .sc-msg-av {
    width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; margin-top: auto;
  }
  .sc-msg.bot .sc-msg-av { background: linear-gradient(135deg, var(--gold-dark), var(--gold)); }
  .sc-msg.user .sc-msg-av { background: #e8e0d0; color: var(--dark); }

  .sc-buble {
    max-width: 82%; padding: 10px 14px;
    border-radius: 18px; font-size: 13px; line-height: 1.55; font-weight: 300;
  }
  .sc-msg.bot .sc-buble {
    background: white; color: var(--dark);
    border-radius: 4px 18px 18px 18px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border: 1px solid rgba(201,169,110,0.15);
  }
  .sc-msg.user .sc-buble {
    background: linear-gradient(135deg, var(--gold-dark), var(--gold));
    color: white; border-radius: 18px 4px 18px 18px;
  }
  .sc-buble a { color: var(--gold-dark); text-decoration: underline; font-weight: 500; }
  .sc-msg.user .sc-buble a { color: var(--gold-light); }

  .sc-quick { display: flex; flex-wrap: wrap; gap: 6px; padding: 2px 14px 10px; flex-shrink: 0; }
  .sc-qbtn {
    background: white; border: 1px solid rgba(201,169,110,0.5);
    color: var(--gold-dark); padding: 6px 12px; border-radius: 20px;
    font-size: 12px; cursor: pointer; font-family: 'Jost', sans-serif;
    transition: all 0.2s; white-space: nowrap;
  }
  .sc-qbtn:hover { background: var(--gold); color: white; border-color: var(--gold); transform: translateY(-1px); }

  .sc-dots {
    background: white; border: 1px solid rgba(201,169,110,0.2);
    padding: 11px 15px; border-radius: 4px 18px 18px 18px;
    display: flex; gap: 4px; align-items: center;
  }
  .sc-dots span {
    width: 6px; height: 6px; background: var(--gold); border-radius: 50%;
    animation: scTyping 1.2s infinite;
  }
  .sc-dots span:nth-child(2) { animation-delay: 0.2s; }
  .sc-dots span:nth-child(3) { animation-delay: 0.4s; }

  .sc-input-area {
    padding: 12px 14px; border-top: 1px solid rgba(201,169,110,0.2);
    background: white; display: flex; gap: 9px; align-items: flex-end; flex-shrink: 0;
  }
  #sc-input {
    flex: 1; border: 1.5px solid rgba(201,169,110,0.35);
    border-radius: 20px; padding: 9px 14px;
    font-family: 'Jost', sans-serif; font-size: 13px; font-weight: 300;
    color: var(--dark); background: var(--cream); outline: none;
    resize: none; max-height: 80px; min-height: 40px; line-height: 1.5;
    transition: border-color 0.2s;
  }
  #sc-input:focus { border-color: var(--gold); }
  #sc-input::placeholder { color: #bbb; }

  #sc-send {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg, var(--gold-dark), var(--gold));
    border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; transition: transform 0.2s;
    box-shadow: 0 3px 12px rgba(201,169,110,0.4);
  }
  #sc-send:hover { transform: scale(1.08); }
  #sc-send:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
  #sc-send svg { fill: white; width: 17px; height: 17px; }

  @keyframes scFadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
  @keyframes scMsgIn  { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
  @keyframes scTyping { 0%,60%,100%{transform:translateY(0);opacity:.4} 30%{transform:translateY(-5px);opacity:1} }
  @keyframes scPulse  { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.3);opacity:.7} }

  @media(max-width:480px){
    #sc-window { width:calc(100vw - 18px); right:9px; bottom:86px; }
    #sc-bubble  { right:20px; bottom:90px; }
  }
</style>

<div id="sc-bubble">
  <div id="sc-hint" onclick="scToggle()">💬 Besoin d'aide pour choisir ?</div>
  <button id="sc-toggle" onclick="scToggle()" aria-label="Assistant shopping">
    <svg class="ic-chat" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z"/></svg>
    <svg class="ic-close" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
  </button>
</div>

<div id="sc-window" role="dialog" aria-label="Assistant Sirine Shopping">
  <div class="sc-header">
    <div class="sc-avatar">✨</div>
    <div>
      <div class="sc-name">Sirine — Conseillère Déco</div>
      <div class="sc-status">En ligne · Réponse instantanée</div>
    </div>
  </div>
  <div class="sc-messages" id="sc-msgs"></div>
  <div class="sc-quick" id="sc-quick"></div>
  <div class="sc-input-area">
    <textarea id="sc-input" placeholder="Posez votre question..." rows="1"
      onkeydown="scKey(event)" oninput="scResize(this)"></textarea>
    <button id="sc-send" onclick="scSend()" title="Envoyer">
      <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
    </button>
  </div>
</div>

<script>
(function(){
  const SYSTEM = `Tu es Sirine, une conseillère shopping virtuelle de Sirine Shopping (sirine-shopping.tn).
BOUTIQUE : Articles de décoration intérieure et accessoires maison élégants en Tunisie. Prix en DT. Livraison 8 DT, 24-48h partout en Tunisie. Paiement à la livraison disponible.
CATÉGORIES :
- Décoration (vases, statues, bougies) → /collections/decoration
- Art de Table (services café, plateaux, bonbonnières) → /collections/art-de-table
- Salle de Bain → /collections/salle-de-bain
- Luminaire (suspensions, veilleuses, lampes) → /collections/luminaire
- Linge de Maison → /collections/linge-de-maison
- Cuisine (porte-épices, ustensiles) → /collections/cuisine
RÈGLES : Réponds en français, chaleureux, max 3 phrases. Mets toujours un lien [Voir →](URL). Contact : +216 28 000 000. Suggère un produit complémentaire. Mets en avant promos et stock limité. Réponds en arabe si le client écrit en arabe.`;

  let isOpen=false, isLoading=false, history=[];
  const msgsEl=document.getElementById('sc-msgs');
  const inputEl=document.getElementById('sc-input');
  const sendBtn=document.getElementById('sc-send');
  const winEl=document.getElementById('sc-window');
  const togBtn=document.getElementById('sc-toggle');
  const hintEl=document.getElementById('sc-hint');
  const quickEl=document.getElementById('sc-quick');

  setTimeout(()=>{ addMsg('bot',"Bonjour ! 👋 Je suis **Sirine**, votre conseillère déco. Comment puis-je vous aider à embellir votre maison ?"); renderQR(["🏠 Nouveautés déco","🎁 Idée cadeau","🚚 Livraison ?","💰 Promos en cours"]); },400);
  setTimeout(()=>{ if(!isOpen){hintEl.style.transition='opacity .5s';hintEl.style.opacity='0';setTimeout(()=>hintEl.style.display='none',500);} },5000);

  window.scToggle=()=>{
    isOpen=!isOpen;
    winEl.classList.toggle('open',isOpen);
    togBtn.classList.toggle('open',isOpen);
    if(isOpen){hintEl.style.display='none';setTimeout(()=>inputEl.focus(),350);}
  };

  function addMsg(role,text){
    const w=document.createElement('div'); w.className=`sc-msg ${role}`;
    const av=document.createElement('div'); av.className='sc-msg-av'; av.textContent=role==='bot'?'✨':'👤';
    const b=document.createElement('div'); b.className='sc-buble'; b.innerHTML=md(text);
    w.appendChild(av); w.appendChild(b);
    msgsEl.appendChild(w); msgsEl.scrollTop=msgsEl.scrollHeight;
  }

  function md(t){
    return t
      .replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>')
      .replace(/\*(.*?)\*/g,'<em>$1</em>')
      .replace(/\[([^\]]+)\]\(([^)]+)\)/g,'<a href="$2" target="_blank">$1</a>')
      .replace(/\n/g,'<br>');
  }

  function showTyping(){
    const d=document.createElement('div'); d.className='sc-msg bot'; d.id='sc-typing';
    d.innerHTML='<div class="sc-msg-av">✨</div><div class="sc-dots"><span></span><span></span><span></span></div>';
    msgsEl.appendChild(d); msgsEl.scrollTop=msgsEl.scrollHeight;
  }
  function hideTyping(){ const e=document.getElementById('sc-typing'); if(e)e.remove(); }

  function renderQR(arr){
    quickEl.innerHTML='';
    arr.forEach(r=>{
      const b=document.createElement('button'); b.className='sc-qbtn'; b.textContent=r;
      b.onclick=()=>{quickEl.innerHTML='';scSend(r);}; quickEl.appendChild(b);
    });
  }

  window.scSend=async(override)=>{
    const text=override||inputEl.value.trim();
    if(!text||isLoading) return;
    inputEl.value=''; inputEl.style.height='auto'; quickEl.innerHTML='';
    addMsg('user',text);
    history.push({role:'user',content:text});
    isLoading=true; sendBtn.disabled=true; showTyping();

    try{
      const res=await fetch('{{ route("chat") }}',{
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||'','Accept':'application/json'},
        body:JSON.stringify({messages:history,system:SYSTEM})
      });
      const data=await res.json();
      hideTyping();
      const reply=data.reply||"Une erreur est survenue. Contactez-nous au **+216 28 000 000**. 💛";
      addMsg('bot',reply);
      history.push({role:'assistant',content:reply});
      const l=reply.toLowerCase();
      if(l.includes('livraison')||l.includes('commande')) renderQR(['🛒 Commander','📞 Contacter','💳 Paiement']);
      else if(l.includes('cadeau')||l.includes('offrir')) renderQR(['🎁 Art de Table','✨ Décoration','💡 Luminaire']);
      else if(l.includes('promo')||l.includes('réduction')) renderQR(['🛍️ Voir les promos','🔥 Meilleures ventes']);
      else if(history.length>=6) renderQR(['🛒 Commander maintenant','📞 Parler à un humain']);
    }catch(e){
      hideTyping();
      addMsg('bot','Une erreur réseau est survenue. Veuillez réessayer. 😊');
    }
    isLoading=false; sendBtn.disabled=false; inputEl.focus();
  };

  window.scKey=(e)=>{if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();scSend();}};
  window.scResize=(el)=>{el.style.height='auto';el.style.height=Math.min(el.scrollHeight,80)+'px';};
})();
</script>
