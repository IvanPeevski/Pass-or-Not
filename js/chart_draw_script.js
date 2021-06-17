function drawChart(cx, cy, radius, arcwidth, max_points, points, undecided, grade_text, grade) {
  let canvas = document.getElementById("canvas");
  let ctx = canvas.getContext("2d");
  var lost_points = max_points - points - undecided;
  var accum = 0;
  var PI = Math.PI;
  var PI2 = PI * 2;
  var offset = -PI / 2;
  ctx.lineWidth = arcwidth;

  ctx.beginPath();
  ctx.arc(cx, cy, radius,
    offset + PI2 * (accum / max_points),
    offset + PI2 * ((accum + points) / max_points)
  ); 
  ctx.strokeStyle = 'green';
  ctx.stroke();
  accum += points;

  ctx.beginPath();
  ctx.arc(cx, cy, radius,
    offset + PI2 * (accum / max_points),
    offset + PI2 * ((accum + lost_points) / max_points)
  ); 
  ctx.strokeStyle = 'red';
  ctx.stroke();
  accum += lost_points;

  ctx.beginPath();
  ctx.arc(cx, cy, radius,
    offset + PI2 * (accum / max_points),
    offset + PI2 * ((accum + undecided) / max_points)
  ); 
  ctx.strokeStyle = 'orange';
  ctx.stroke();
  accum += undecided;

  var innerRadius = radius - arcwidth - 3;
  ctx.beginPath();
  ctx.arc(cx, cy, innerRadius, 0, PI2);
  ctx.fillStyle = '#28a745';
  ctx.fill();
  ctx.textAlign = 'center';
  ctx.textBaseline = 'bottom';
  ctx.fillStyle = 'white';
  ctx.font = (innerRadius / 1.5) + 'px verdana';
  ctx.fillText(grade, cx, cy + innerRadius * .7);
  ctx.font = (innerRadius / 4) + 'px verdana';
  ctx.fillText(grade_text, cx, cy - innerRadius * .25);
}