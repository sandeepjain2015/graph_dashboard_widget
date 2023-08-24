import { useState, useEffect, useCallback, render } from "@wordpress/element";
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
import { SelectControl } from "@wordpress/components";
import { __ } from "@wordpress/i18n";
import apiFetch from "@wordpress/api-fetch";

const GraphWidget = () => {
	const [loading, setLoading] = useState(true);
	const [selectedValue, setSelectedValue] = useState("7days");
	const [data, setData] = useState([]);
	const [error, setError] = useState(null);

	const fetchData = useCallback(async () => {
		try {
			const responseData = await apiFetch({
				path: `/graph-widget/v1/data?period=${selectedValue}`,
			});
			setData(responseData);
			setLoading(false);
			setError(null);
		} catch (error) {
			console.error(error);
			setLoading(false);
			setError(__("Error fetching data", "graph-dashboard-widget"));
		}
	}, [selectedValue]);

	useEffect(() => {
		fetchData();
	}, [fetchData]);

	const handleSelectChange = (newValue) => {
		setSelectedValue(newValue);
	};

	return (
		<div className="border border-secondary m-5 p-5">
			<div className="row mb-3">
				<SelectControl
					options={[
						{ label: __("Last 7 days", "graph-dashboard-widget"), value: "7days" },
						{ label: __("Last 15 days", "graph-dashboard-widget"), value: "15days" },
						{ label: __("Last 1 month", "graph-dashboard-widget"), value: "1month" },
					]}
					value={selectedValue}
					onChange={handleSelectChange}
					className="form-select form-select-lg"
				/>
				<LineChartComponent loading={loading} error={error} data={data} />
			</div>
		</div>
	);
};

const LineChartComponent = ({ loading, error, data }) => {
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
					{__("No data available. Please check again later", "graph-dashboard-widget")}
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

window.addEventListener(
	"load",
	() => {
		const widgetContainer = document.querySelector(
			"#dashboard-widget-container"
		);
		if (widgetContainer) {
			render(<GraphWidget />, widgetContainer);
		}
	},
	false
);
