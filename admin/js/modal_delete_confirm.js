document.addEventListener('DOMContentLoaded', function() {
  const deleteButtons = document.querySelectorAll('.delete-btn');
  const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

  let deleteSubjectId;

  // Add event listener to delete buttons to store subject ID
  deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
      deleteSubjectId = this.getAttribute('data-subject-id');
      $('#deleteConfirmationModal').modal('show');
    });
  });

  // Add event listener to confirm delete button
  confirmDeleteBtn.addEventListener('click', function() {
    // Perform deletion action
    $.ajax({
      url: '../tools/delete.php',
      method: 'POST',
      data: { delete_subject: deleteSubjectId },
      success: function(response) {
        // Handle success, e.g., display success message or reload page
        window.location.reload(); // Reload the page after successful deletion
      },
      error: function(xhr, status, error) {
        // Handle error, e.g., display error message
        console.error(xhr.responseText);
      }
    });
  });
});