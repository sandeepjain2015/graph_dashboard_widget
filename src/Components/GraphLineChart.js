import {
	ResponsiveContainer,
	LineChart,
	Line,
	XAxis,
	YAxis,
	Tooltip,
	Legend,
	CartesianGrid,
} from "recharts";
import { __ } from "@wordpress/i18n";
const GraphLineChart = ({ loading, error, data }) => {
	const hasData = data.length > 0;
	return (
		<div className="row">
			{loading ? (
				<div>{__("Loading...", "graph-dashboard-widget")}</div>
			) : error ? (
				<div className="text-danger">
					{__("Error", "graph-dashboard-widget")}: {error}
				</div>
			) : !hasData ? (
				<div>
					{__(
						"No data available. Please check again later",
						"graph-dashboard-widget"
					)}
				</div>
			) : (
				<ResponsiveContainer width="100%" aspect={3}>
					<LineChart width={500} height={300} data={data}>
						<XAxis dataKey="name" />
						<YAxis />
						<CartesianGrid stroke="#ccc" />
						<Tooltip contentStyle={{ backgroundColor: "yellow" }} />
						<Legend />
						<Line
							dataKey="students"
							name="Students"
							stroke="red"
							activeDot={{ r: 2 }}
						/>
						<Line
							dataKey="fees"
							name="Fees"
							stroke="green"
							activeDot={{ r: 2 }}
						/>
					</LineChart>
				</ResponsiveContainer>
			)}
		</div>
	);
};

export default GraphLineChart;
