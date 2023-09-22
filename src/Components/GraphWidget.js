import { useState, useEffect, useCallback } from "@wordpress/element";
import GraphLineChart from "./GraphLineChart";
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
						{
							label: __("Last 7 days", "graph-dashboard-widget"),
							value: "7days",
						},
						{
							label: __("Last 15 days", "graph-dashboard-widget"),
							value: "15days",
						},
						{
							label: __("Last 1 month", "graph-dashboard-widget"),
							value: "1month",
						},
					]}
					value={selectedValue}
					onChange={handleSelectChange}
					className="form-select form-select-lg"
				/>
				<GraphLineChart loading={loading} error={error} data={data} />
			</div>
		</div>
	);
};
export default GraphWidget;
