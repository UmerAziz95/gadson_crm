$('#add_manager_btn').click(function(){
    window.location = '/admin/managers';
});
$('#add_building_btn').click(function(){
    window.location = '/admin/addBuilding';
});
$('#add_appartment_btn').click(function(){
    window.location = '/admin/addAppartment';
});
$('#add_task_btn').click(function(){
    window.location = '/admin/tasks/add';
});
$('#assign_tasks_btn').click(function(){
    window.location = '/admin/tasks';
});




if ($("#taskChart").length) {
    var chart = echarts.init(document.getElementById("taskChart"));
var date = new Date();
    var option = {
        responsive: true,
        title: {
            text: "Task Analytics Last 15 Days (" + new Date().getFullYear() + ")",
            subtext: ""
        },
        tooltip: {
            trigger: "axis",
            axisPointer: {
                type: "shadow"
            },
            formatter: function(params) {
                var date = new Date(params[0].name); 
                var formattedDate = date.getDate() + ', ' + date.toLocaleString('default', { month: 'short' });
                var tooltip = formattedDate + "<br/>"; 
                params.forEach(function(item) {
                    tooltip += item.seriesName + ": " + item.value + "<br/>";
                });
                return tooltip;
            }
        },
        legend: {
            x: 220,
            y: 40,
            data: ["Cancelled", "Assigned", "Working On", "Hold", "Stuck", "Done"]
        },
        color: ["#FF0000", "#89BE3A", "#FFC300", "#FF5733", "#9B59B6", "#2ECC71"],
        toolbox: {
            show: true,
            feature: {
                magicType: {
                    show: true,
                    title: {
                        line: "Line",
                        bar: "Bar",
                        stack: "Stack",
                        tiled: "Tiled"
                    },
                    type: ["line", "bar", "stack", "tiled"]
                },
                restore: {
                    show: true,
                    title: "Restore"
                },
                saveAsImage: {
                    show: true,
                    title: "Save Image"
                }
            }
        },
        calculable: true,
        xAxis: [{
            type: "category",
            data: taskData.map(item => item.task_date), // Assuming data contains task_date array
            axisLabel: {
                interval: 0,
                rotate: 0,
                formatter: function(value) {
                    var date = new Date(value);
                    return `${date.getDate()}, ${date.toLocaleString('default', { month: 'short' })}`;
                }
            },
        }],
        yAxis: [{
            type: "value"
        }],
        series: [
            { name: "Draft", type: "bar", stack: 'total', data: taskData.map(item => item.draft_count) },
            { name: "Assigned", type: "bar", stack: 'total', data: taskData.map(item => item.assigned_count) },
            { name: "Working On", type: "bar", stack: 'total', data: taskData.map(item => item.working_on_count) },
            { name: "Hold", type: "bar", stack: 'total', data: taskData.map(item => item.hold_count) },
            { name: "Stuck", type: "bar", stack: 'total', data: taskData.map(item => item.stuck_count) },
            { name: "Done", type: "bar", stack: 'total', data: taskData.map(item => item.done_count) },
            { name: "Cancelled", type: "bar", stack: 'total', data: taskData.map(item => item.cancelled_count) }
        ]
    };

    chart.setOption(option);
}
