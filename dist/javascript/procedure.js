function updateEstimatedTime() {
  const procedureTimes = {
    Braces: 30, // Minutes per tooth
    Xray: 10, // Minutes per X-ray, assuming not per tooth
    Consultation: 15, // Fixed time, not per tooth
    Extraction: 45, // Minutes per tooth
    "Root Canal Treatment": 60, // Minutes per tooth
    "Teeth Whitening": 20, // Minutes per tooth
    Surgery: 40, // Fixed time, assuming not per tooth
  };

  let procedure = document.getElementById("procedure").value;
  let numberOfTeeth =
    parseInt(document.getElementById("teeth_number").value) || 1; // Default to 1 if no input

  // Calculate time based on the procedure and number of teeth
  let baseTime = procedureTimes[procedure] || 0; // Get base time or default to 0 if procedure is not found
  let totalTime = baseTime;

  // Procedures that depend on the number of teeth to calculate total time
  if (
    [
      "Braces",
      "Extraction",
      "Root Canal Treatment",
      "Teeth Whitening",
    ].includes(procedure)
  ) {
    totalTime = baseTime * numberOfTeeth;
  }

  // Update the estimated time input
  document.getElementById("estimated_time").value = totalTime + " mins";
}

// Add event listeners to both the procedure and teeth_number fields
document
  .getElementById("procedure")
  .addEventListener("change", updateEstimatedTime);
document
  .getElementById("teeth_number")
  .addEventListener("input", updateEstimatedTime);
