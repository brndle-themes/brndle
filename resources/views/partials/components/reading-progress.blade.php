<div id="brndle-progress" class="fixed top-0 left-0 right-0 z-[60] h-0.5 bg-[var(--color-accent)] origin-left scale-x-0 transition-transform" style="transform-origin: left;"></div>
<script>
(function(){var p=document.getElementById('brndle-progress');if(!p)return;window.addEventListener('scroll',function(){var h=document.documentElement;var pct=h.scrollTop/(h.scrollHeight-h.clientHeight);p.style.transform='scaleX('+Math.min(pct,1)+')';},{passive:true});})();
</script>
