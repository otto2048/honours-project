window.onload = preparePage();

function preparePage()
{
    interact('.resize').resizable({
        // resize from all edges and corners
        edges: { left: true, right: true, bottom: true, top: true },
    
        listeners: {
          move (event) {
            var target = event.target
            var x = (parseFloat(target.getAttribute('data-x')) || 0)
            var y = (parseFloat(target.getAttribute('data-y')) || 0)
    
            // update the element's style
            target.style.width = event.rect.width + 'px'
            target.style.height = event.rect.height + 'px'
    
            // translate when resizing from top or left edges
            x += event.deltaRect.left
            y += event.deltaRect.top
    
            target.style.transform = 'translate(' + x + 'px,' + y + 'px)'
    
            target.setAttribute('data-x', x)
            target.setAttribute('data-y', y)
            
            window.dispatchEvent(new Event('resize'));
          }
        },
    
        inertia: true
      });
}