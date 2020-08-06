//This javascript file is used for creating simple functions, that either hides or shows the create forms in the user profile page
function showTopicForm() {
  var topicForm = document.getElementById("createTopicArea");
  if (topicForm.style.display !== "block") {
    topicForm.style.display = "block";
  } else {
    topicForm.style.display = "none";
  }
}

function showEntryForm() {
  var entryForm = document.getElementById("createEntryArea");
  if (entryForm.style.display !== "block") {
    entryForm.style.display = "block";
  } else {
    entryForm.style.display = "none";
  }
}
