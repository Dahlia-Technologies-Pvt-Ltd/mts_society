import React, { useState, useEffect } from "react";
import {
    Grid,
    FormControlLabel,
    Button,
    RadioGroup,
    Typography,
    FormGroup,
    Switch,
    Box,
    Paper,
    Alert,
    IconButton,
    AlertTitle,CircularProgress,Checkbox
} from "@mui/material";
import { IconMinus, IconPlus, IconTrash } from '@tabler/icons';
import { Portal } from '@mui/base';
import Snackbar from "@mui/material/Snackbar";
import { useParams, Link, useNavigate } from "react-router-dom";
import PageContainer from "@src/components/container/PageContainer";
import Breadcrumb from "@src/layouts/full/shared/breadcrumb/Breadcrumb";
import CustomTextField from "@src/components/forms/theme-elements/CustomTextField";
import axios from "axios";
import * as yup from "yup";
import { useFormik } from "formik";
import CustomFormLabel from "@src/components/forms/theme-elements/CustomFormLabel";
import ParentCard from "@src/components/shared/ParentCard";
import "react-quill/dist/quill.snow.css";
import ReactQuill from "react-quill";

const AddSubscription = () => {
    const [isLoading, setIsLoading] = useState(false);
    const [isErrorVisible, setIsErrorVisible] = useState(false);
    const [isSuccessVisible, setIsSuccessVisible] = useState(false);
    const [errorMessage, setErrorMessage] = useState("");
    const [successMessage, setSuccessMessage] = useState("");
    const { id } = useParams(); // Access the 'id' parameter from the URL if it exists
    const navigate = useNavigate();
    const [quillText, setQuillText] = useState("");

    const BCrumb = [
        {
            to: "/super-admin/subscription-plan-list",
            title: "Subscription Plan List",
        },
        {
            title: id ? "Edit Subscription" : "Add Subscription",
        },
    ];

    const validationSchema = yup.object().shape({
        subscription_plan: yup.string().required("Subscription plan is required"),
        price: yup.string().required("Price is required"),
        frequency: yup.string().required("Frequency is required"),
        features: yup.string().required("Features is required"),
    });

    const formik = useFormik({
        initialValues: {
            subscription_plan: "",
            price: "",
            frequency:'',
            is_renewal_plan: '0',
            features: '',
        },
        validationSchema,
        onSubmit: async (values) => {
            try {
                setIsLoading(true);
                // Create a FormData object
                const formData = new FormData();
                // Append form fields to the FormData object
                formData.append("subscription_plan", values.subscription_plan);
                formData.append("price", values.price);
                formData.append("frequency", values.frequency);
                formData.append("is_renewal_plan", values.is_renewal_plan);
                const cleanFeatures = values.features.replace(/<[^>]*>/g, '');
                formData.append('features', cleanFeatures);

                // Determine the API URL based on whether it's an edit or add operation
                const appUrl = import.meta.env.VITE_API_URL;
                let API_URL = appUrl + "/api/add-master-subscription";
                (id) ? formData.append("id", id) : '';
                // Make the API POST request
                const token = localStorage.getItem("authToken");
                const response = await axios.post(API_URL, formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "multipart/form-data",
                    },
                });
                setIsSuccessVisible(true);
                setSuccessMessage(response.data.message);
                sessionStorage.setItem("successMessage", response.data.message);
                navigate("/super-admin/subscription-plan-list");
            } catch (error) {
                // Handle errors here
                setIsErrorVisible(true);
                const validationErrors = error.response.data.validation_error;
                const errorMessages = [];
                // Iterate through each field in the validation_errors object
                for (const field in validationErrors) {
                    if (validationErrors.hasOwnProperty(field)) {
                        const errorMessage = validationErrors[field][0];
                        errorMessages.push(errorMessage);
                    }
                }
                const concatenatedErrorMessage = errorMessages.join("\n");
                setErrorMessage(concatenatedErrorMessage);
                setTimeout(() => {
                    setIsErrorVisible(false);
                }, 5000);
                console.error("Edit spare part error:", error);
            }finally{
                setIsLoading(false);
            }
        },
    });

    // Fetch existing data if it's an edit operation
    useEffect(() => {
        if (id) {
            fetchData();
        }
    }, [id]);

    const fetchData = async () => {
        try {
            const appUrl = import.meta.env.VITE_API_URL;
            const API_URL = `${appUrl}/api/show-master-subscription/${id}`;
            const token = localStorage.getItem("authToken");

            const response = await axios.get(API_URL, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "multipart/form-data",
                },
            });

            const data = response.data.data;
            formik.setValues({
                subscription_plan: data.subscription_plan,
                price: data.price,
                frequency: data.frequency,
                is_renewal_plan: (data.is_renewal_plan == 'Renewed') ? '1' : '0',
                features: data.features,
            });
            setQuillText(data.features);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    return (
        <>
            <PageContainer
                title="Subscription Plan"
                description="This is Subscription Plan"
            >
            <Breadcrumb title="" items={BCrumb} />
            <Portal>
                <Snackbar
                    anchorOrigin={{ vertical: "top", horizontal: "right" }}
                    open={isErrorVisible}
                    autoHideDuration={3000}
                    onClose={() => setIsErrorVisible(false)}
                >
                    <Alert severity="error">
                        <div style={{ fontSize: "14px", padding: "2px" }}>
                            {errorMessage && <div>{errorMessage}</div>}
                        </div>
                    </Alert>
                </Snackbar>
            </Portal>
            <Portal>
                <Snackbar
                    anchorOrigin={{ vertical: "top", horizontal: "right" }}
                    open={isSuccessVisible}
                    autoHideDuration={3000}
                    onClose={() => setIsSuccessVisible(false)}
                >
                <Alert severity="success">
                    <div style={{ fontSize: "14px", padding: "2px" }}>
                        {successMessage && <div>{successMessage}</div>}
                    </div>
                </Alert>
                </Snackbar>
            </Portal>
            <ParentCard
                title={
                    id
                        ? "Edit Subscription Plan"
                        : "Add Subscription Plan"
                }
            >
                <form onSubmit={formik.handleSubmit}>
                    <Grid container spacing={2}>
                        {/* 1 */}
                        <Grid item xs={6}>
                            <CustomFormLabel
                                htmlFor="subscription_plan"
                                sx={{ mt: 0 }}
                            >
                                Subscription Plan <span style={{color:'red'}}>*</span>
                            </CustomFormLabel>
                            <CustomTextField
                                id="subscription_plan"
                                name="subscription_plan"
                                placeholder="Subscription Plan"
                                fullWidth
                                value={formik.values.subscription_plan}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.subscription_plan &&
                                    Boolean(formik.errors.subscription_plan)
                                }
                                helperText={
                                    formik.touched.subscription_plan &&
                                    formik.errors.subscription_plan
                                }
                            />
                        </Grid>        
                        <Grid item xs={6}>
                            <CustomFormLabel
                                htmlFor="price"
                                sx={{ mt: 0 }}
                            >
                                Price <span style={{color:'red'}}>*</span>
                            </CustomFormLabel>
                            <CustomTextField
                                id="price"
                                name="price"
                                placeholder="Price"
                                fullWidth
                                value={formik.values.price}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.price &&
                                    Boolean(formik.errors.price)
                                }
                                helperText={
                                    formik.touched.price &&
                                    formik.errors.price
                                }
                                onKeyDown={(e) => {
                                if (!(e.key === 'e' || e.key === '-' || e.key === '+' || !isNaN(Number(e.key)) || e.key === 'Backspace')) {
                                    e.preventDefault();
                                }
                                }}  
                            />
                        </Grid>
                        <Grid item xs={6}>
                            <CustomFormLabel
                                htmlFor="frequency"
                                sx={{ mt: 0 }}
                            >
                                Frequency <span style={{color:'red'}}>*</span>
                            </CustomFormLabel>
                            <CustomTextField
                                id="frequency"
                                name="frequency"
                                placeholder="Frequency"
                                fullWidth
                                value={formik.values.frequency}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.frequency &&
                                    Boolean(formik.errors.frequency)
                                }
                                helperText={
                                    formik.touched.frequency &&
                                    formik.errors.frequency
                                }
                                onKeyDown={(e) => {
                                if (!(e.key === 'e' || e.key === '-' || e.key === '+' || !isNaN(Number(e.key)) || e.key === 'Backspace')) {
                                    e.preventDefault();
                                }
                                }}
                            />
                        </Grid>
                        <Grid item xs={6}>
                            <FormControlLabel
                                control={
                                    <Checkbox
                                        id="is_renewal_plan"
                                        name="is_renewal_plan"
                                        checked={formik.values.is_renewal_plan === '1'}
                                        onChange={(e) => {
                                            formik.setFieldValue(
                                                'is_renewal_plan',
                                                e.target.checked ? '1' : '0'
                                            );
                                        }}
                                    />
                                }
                                label="Is Renewal Plan"
                                sx={{ mt: 4 }}
                            />
                        </Grid>
                        <Grid item xs={12}>
                            <CustomFormLabel
                                htmlFor="features"
                                sx={{ mt: 0 }}
                            >
                                Features <span style={{ color: 'red' }}>*</span>
                            </CustomFormLabel>
                            <ReactQuill
                                style={{ height: "300px" }}
                                value={quillText}
                                onChange={(value) => {
                                    setQuillText(value);
                                    formik.setFieldValue(
                                        "features",
                                        value
                                    );
                                }}
                                placeholder="Type here..."
                            />
                        </Grid>
                        {/* Display error message below the ReactQuill component */}
                        {formik.touched.features && formik.errors.features && (
                            <Typography variant="caption" color="error" sx={{ mt: 6, ml: 3 }}>
                                {formik.errors.features}
                            </Typography>
                        )}

                        {/* Submit Button */}
                        <Grid item xs={12} mt={5} display={'flex'} alignItems={'center'} justifyContent={'center'}>
                            <Link to="/super-admin/subscription-plan-list">
                                <Button
                                    color="warning"
                                    variant="contained"
                                    style={{ marginRight: "10px" }}
                                >
                                    Back
                                </Button>
                            </Link>
                            <Button
                                variant="contained"
                                color="primary"
                                type="submit"
                                disabled={isLoading}
                            >
                                {id ? "Update" : "Submit"}
                                {isLoading && <CircularProgress size={24} color="inherit" />}
                            </Button>
                        </Grid>
                    </Grid>
                </form>
                </ParentCard>
            </PageContainer>
        </>
    );
};

export default AddSubscription;
