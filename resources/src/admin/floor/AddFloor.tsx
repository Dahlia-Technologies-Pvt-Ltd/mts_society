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
import AddIcon from '@mui/icons-material/Add';
import RemoveIcon from '@mui/icons-material/Remove';
import DeleteIcon from '@mui/icons-material/Delete';
import { useApiMessages } from '@src/common/Utils'; // Import the utility

const AddFloor = () => {
    const { showSuccessMessage, showErrorMessage, renderSuccessMessage, renderErrorMessage } = useApiMessages();
    const [isLoading, setIsLoading] = useState(false);
    // const [isErrorVisible, setIsErrorVisible] = useState(false);
    // const [isSuccessVisible, setIsSuccessVisible] = useState(false);
    // const [errorMessage, setErrorMessage] = useState("");
    // const [successMessage, setSuccessMessage] = useState("");
    const [wingsData, setWingsdata] = useState([]);
    const { id } = useParams(); // Access the 'id' parameter from the URL if it exists
    const navigate = useNavigate();
    const [quillText, setQuillText] = useState("");
    //Site Tokens
    const token = localStorage.getItem("authToken");
    const society_token = localStorage.getItem("societyToken");

    const BCrumb = [
        {
            to: "/admin/floor-list",
            title: "Floor List",
        },
        {
            title: id ? "Edit Floor" : "Add Floor",
        },
    ];

    const validationSchema = yup.object().shape({
        floor: yup.string().required("Floor is required"),
    });

    const formik = useFormik({
        initialValues: {
            floor: "",
        },
        validationSchema,
        onSubmit: async (values) => {
            try {
                setIsLoading(true);
                // Create a FormData object
                const formData = new FormData();
                // Append form fields to the FormData object
                formData.append("floor_name", values.floor);
                // Determine the API URL based on whether it's an edit or add operation
                const appUrl = import.meta.env.VITE_API_URL;
                let API_URL = appUrl + "/api/add-floor";
                (id) ? formData.append("id", id) : '';
                // Make the API POST request
                const response = await axios.post(API_URL, formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        "Content-Type": "multipart/form-data",
                        "society_id": `${society_token}`,
                    },
                });
                sessionStorage.setItem("successMessage", response.data.message);
                navigate("/admin/floor-list");
            } catch (error) {
                // Handle errors here
                showErrorMessage(error);
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
            const API_URL = `${appUrl}/api/show-floor/${id}`;
            const response = await axios.get(API_URL, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    "Content-Type": "multipart/form-data",
                    "society_id": `${society_token}`,
                },
            });

            const data = response.data.data;
            formik.setValues({
                floor: data.floor_name,
            });
            setWingsdata(data.wing);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    };

    return (
        <>
            <PageContainer
                title="Floor"
                description="This is Floor"
            >
            <Breadcrumb title="" items={BCrumb} />
            {renderSuccessMessage()}
            {renderErrorMessage()}
            <ParentCard
                title={
                    id
                        ? "Edit Floor"
                        : "Add Floor"
                }
            >
                <form onSubmit={formik.handleSubmit}>
                    <Grid container spacing={2}>
                        {/* 1 */}
                        <Grid item xs={7}>
                            <CustomFormLabel
                                htmlFor="floor"
                                sx={{ mt: 0 }}
                            >
                                Floor <span style={{color:'red'}}>*</span>
                            </CustomFormLabel>
                            <CustomTextField
                                id="floor"
                                name="floor"
                                placeholder="Floor"
                                fullWidth
                                value={formik.values.floor}
                                onChange={formik.handleChange}
                                error={
                                    formik.touched.floor &&
                                    Boolean(formik.errors.floor)
                                }
                                helperText={
                                    formik.touched.floor &&
                                    formik.errors.floor
                                }
                            />
                        </Grid>        
                       
                        
                        {/* Submit Button */}
                        <Grid item xs={12} mt={5} display={'flex'} alignItems={'center'} justifyContent={'center'}>
                            <Link to="/admin/floor-list">
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

export default AddFloor;
