import { render } from "@wordpress/element";
import GraphWidget from "./Components/GraphWidget"
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
