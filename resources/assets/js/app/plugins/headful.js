import headful from 'headful';
import { APP_NAME } from 'prontogioco/constants';

export default function(params) {
	params.title = params.title ? `${params.title} - ${APP_NAME}` : APP_NAME;

	headful(params);
}