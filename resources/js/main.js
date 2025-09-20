import { nextTick } from "vue";
import { router } from "@inertiajs/vue3";
import $ from "jquery";
import { useToast } from "vue-toastification";
const toast = useToast();

export const store = (submit_route, form_data) => {
    axios
        .post(route(submit_route), form_data, { headers: { 'Content-Type': 'multipart/form-data' } })
        .then((response) => {
            // toast.success("Product Successfully Created");
            toast.success(response.data.message);
            setTimeout(() => {
                window.location.href = response.data.redirect;
            }, 1500);
        })
        .catch((error) => {
            if (error.response) {
                const { message, errors } = error.response.data;
                if (message) {
                    toast.error(message);
                }
            } else {
                toast.error("Something went wrong. Please try again.");
            }
        });
};

export const update = (update_route, form_data, section_name) => {
    axios
        .post(route(update_route), form_data)
        .then((response) => {
            toast.success(response.data.message);
            setTimeout(() => {
                window.location.href = response.data.redirect;
            }, 1500);
        })
        .catch((error) => {
            toast.error("Error updating", section_name, error);
        });
};

export const fetchData = async (url, dataRef) => {
    try {
        const response = await axios.post(url);
        dataRef.value = response.data.data;
    } catch (error) {
        console.error(`Error fetching data from ${url}:`, error);
    }
};

export const searchProduct = async (url) => {
    try {
        const response = await axios.get(url);
        return response.data;
    } catch (error) {
            console.error("An error occurred:", error);
        return [];
    }

}