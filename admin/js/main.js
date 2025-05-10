document.addEventListener('DOMContentLoaded', function() {
  const homeNavigation = document.querySelector('.home');
  const collapseButton = document.querySelector('#collapse_btn');
  const sidebarState = localStorage.getItem('sidebarCollapsed');

  // Function to toggle sidebar collapse
  function toggleCollapse() {
    homeNavigation.classList.toggle('collapse');
    localStorage.setItem('sidebarCollapsed', homeNavigation.classList.contains('collapse'));
  }

  // Set initial sidebar state based on localStorage
  if (sidebarState === 'true') {
    homeNavigation.classList.add('collapse');
  }

  // Event listener for collapse button
  collapseButton.addEventListener('click', function(event) {
    event.stopPropagation(); // Prevents triggering parent click events
    toggleCollapse();
  });

  // Event listener for input[type=number]
  // document.querySelector("input[type=number]").addEventListener('input', function(e) {
  //   console.log(new Date(e.target.valueAsNumber, 0, 1));
  // });
});
