import { APP_NAME } from 'prontogioco/constants';

export default function(title) {
	document.title = title ? `${title} - ${APP_NAME}` : APP_NAME;
}